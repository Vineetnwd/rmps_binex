<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$branch_id = $_SESSION['branch_id'] ?? $branch_id;

// Fetch distinct teacher-class-subject mappings from timetable
$ts_sql = "SELECT DISTINCT teacher_id, student_class AS class_name, student_section AS section_name, subject AS subject_name FROM timetable WHERE status='ACTIVE' AND branch_id='$branch_id' AND teacher_id IS NOT NULL AND teacher_id != ''";
$ts_res = direct_sql($ts_sql);

$matrix_data = [];

if ($ts_res['count'] > 0) {
    foreach ($ts_res['data'] as $row) {
        $t_id    = $row['teacher_id'];
        $c_name  = $row['class_name'];
        $s_name  = $row['section_name'];
        $subj_id = $row['subject_name'];

        // Resolve teacher name
        $t_info       = get_data('employee', $t_id, 'name');
        $teacher_name = ($t_info['status'] == 'success') ? $t_info['data'] : 'Unknown Teacher';

        // Resolve subject name
        if (is_numeric($subj_id)) {
            $subj_id_clean = intval(trim($subj_id));
            $sub_db        = direct_sql("SELECT subject_name FROM subject WHERE id = $subj_id_clean", 'get', false);
            $subject_name  = ($sub_db['count'] > 0 && !empty($sub_db['data'][0]['subject_name'])) ? $sub_db['data'][0]['subject_name'] : $subj_id;
        } else {
            $subject_name = $subj_id;
        }

        // ────────────────────────────────────────────────────────────────
        // STEP 1: Fetch active students in this class-section
        // ────────────────────────────────────────────────────────────────
        $students_q  = direct_sql("SELECT id FROM student WHERE student_class='$c_name' AND student_section='$s_name' AND status='ACTIVE' AND branch_id='$branch_id'");
        $total_students = $students_q['count'];
        $student_ids    = ($total_students > 0) ? array_column($students_q['data'], 'id') : [];

        // ────────────────────────────────────────────────────────────────
        // STEP 2: Fetch UT tests for this class-subject
        // ────────────────────────────────────────────────────────────────
        $ut_q = direct_sql("SELECT id, ut_number, max_marks FROM ut_tests WHERE student_class='$c_name' AND (student_section='' OR student_section IS NULL) AND subject='$subject_name' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY CAST(ut_number AS UNSIGNED) ASC");

        // ────────────────────────────────────────────────────────────────
        // STEP 3: Collect all marks per student per UT
        // Class Avg % = (Sum of all marks) / (Total Students × 20) × 100
        //   where 20 = max marks per UT (fixed per formula)
        // ────────────────────────────────────────────────────────────────
        $sum_all_marks    = 0;
        $marks_count      = 0;         // how many marks entries actually recorded
        $ut_phase_avgs    = [];        // per-UT average percentage across all students
        $student_ut_pcts  = [];        // [student_id][ut_index] => pct for low-performer calc

        if ($ut_q['count'] > 0 && $total_students > 0) {
            foreach ($ut_q['data'] as $ut) {
                $ut_id     = $ut['id'];
                $ut_max    = floatval($ut['max_marks']) ?: 20; // fallback to 20

                $marks_res = direct_sql("SELECT student_id, marks FROM ut_marks WHERE ut_id='$ut_id'");
                $ut_total  = 0;
                $ut_cnt    = 0;

                if ($marks_res['count'] > 0) {
                    foreach ($marks_res['data'] as $m) {
                        $sid   = $m['student_id'];
                        $score = floatval($m['marks']);
                        $pct   = ($ut_max > 0) ? ($score / $ut_max) * 100 : 0;

                        $sum_all_marks += $score;
                        $marks_count++;
                        $ut_total += $pct;
                        $ut_cnt++;
                        $student_ut_pcts[$sid][] = $pct;
                    }
                }
                if ($ut_cnt > 0) {
                    $ut_phase_avgs[] = $ut_total / $ut_cnt;
                }
            }
        }

        // Formula: Class Avg % = sum(marks) / (Total Students × 20) × 100
        $fixed_max_per_ut = 20;
        $num_uts          = $ut_q['count'];
        $denominator      = $total_students * $fixed_max_per_ut * $num_uts;
        $class_avg        = ($denominator > 0) ? ($sum_all_marks / $denominator) * 100 : null;

        // ────────────────────────────────────────────────────────────────
        // STEP 4: Improvement = Current Phase Avg % - Previous Phase Avg %
        // Split UTs into two halves: first half = previous phase, second = current
        // ────────────────────────────────────────────────────────────────
        $improvement = 0;
        if (count($ut_phase_avgs) >= 2) {
            $half       = ceil(count($ut_phase_avgs) / 2);
            $prev_half  = array_slice($ut_phase_avgs, 0, $half);
            $curr_half  = array_slice($ut_phase_avgs, $half);
            $prev_avg   = array_sum($prev_half) / count($prev_half);
            $curr_avg   = (count($curr_half) > 0) ? array_sum($curr_half) / count($curr_half) : $prev_avg;
            $improvement = round($curr_avg - $prev_avg, 1);
        } elseif (count($ut_phase_avgs) == 1) {
            $improvement = 0;
        }

        // ────────────────────────────────────────────────────────────────
        // STEP 5: Low Performer Improvement
        // = (Students moved from <35% to >50%) / Total Students × 100
        // Compare first-half avg vs second-half avg per student
        // ────────────────────────────────────────────────────────────────
        $low_performer_improvement = 0;
        if ($total_students > 0 && count($ut_phase_avgs) >= 2) {
            $half = ceil($num_uts / 2);
            $moved = 0;
            foreach ($student_ut_pcts as $sid => $pcts) {
                $prev_pcts = array_slice($pcts, 0, $half);
                $curr_pcts = array_slice($pcts, $half);
                if (empty($prev_pcts) || empty($curr_pcts)) continue;
                $prev_student_avg = array_sum($prev_pcts) / count($prev_pcts);
                $curr_student_avg = array_sum($curr_pcts) / count($curr_pcts);
                if ($prev_student_avg < 35 && $curr_student_avg > 50) {
                    $moved++;
                }
            }
            $low_performer_improvement = ($moved / $total_students) * 100;
        }

        // ────────────────────────────────────────────────────────────────
        // STEP 6: Punctuality Score = 100 - (Days Late × 10), min 0
        // "Days Late" = days where teacher's class had 0% attendance marked
        // We approximate using the student_att table for the class-section
        // ────────────────────────────────────────────────────────────────
        $days_late       = 0;
        $punctuality_score = 100;

        if (count($student_ids) > 0) {
            $ids_str = implode("','", $student_ids);
            $att_q   = direct_sql("SELECT * FROM student_att WHERE student_id IN ('$ids_str') AND status='ACTIVE' AND branch_id='$branch_id'");

            if ($att_q['count'] > 0) {
                // Build a map: day => [P count, A count]
                $day_map = [];
                foreach ($att_q['data'] as $att_row) {
                    for ($d = 1; $d <= 31; $d++) {
                        $val = strtoupper(trim($att_row["d_$d"] ?? ''));
                        if ($val === 'P' || $val === 'A') {
                            if (!isset($day_map[$d])) $day_map[$d] = ['p' => 0, 'a' => 0];
                            if ($val === 'P') $day_map[$d]['p']++;
                            else $day_map[$d]['a']++;
                        }
                    }
                }
                // Days late = days where ALL students were absent (0% attendance on that day)
                foreach ($day_map as $d => $counts) {
                    $total_day = $counts['p'] + $counts['a'];
                    if ($total_day > 0 && $counts['p'] == 0) {
                        $days_late++;
                    }
                }
            }
        }
        $punctuality_score = max(0, 100 - ($days_late * 10));

        // ────────────────────────────────────────────────────────────────
        // STEP 7: Final Score (exact user formula)
        // = (Class Avg% × 0.50) + (Low Performer Improvement × 0.30) + (Punctuality × 0.20)
        // ────────────────────────────────────────────────────────────────
        $class_avg_final = $class_avg ?? 0;
        $final_score = ($class_avg_final * 0.50) + ($low_performer_improvement * 0.30) + ($punctuality_score * 0.20);

        $matrix_data[] = [
            'teacher'     => $teacher_name,
            'class'       => "$c_name-$s_name",
            'subject'     => $subject_name,
            'class_avg'   => round($class_avg_final, 1),
            'improvement' => round($improvement, 1),
            'low_perf_imp'=> round($low_performer_improvement, 1),
            'punctuality' => round($punctuality_score, 0),
            'final_score' => round($final_score, 1),
            'days_late'   => $days_late,
            'has_data'    => ($class_avg !== null),
        ];
    }
}

// Sort by Final Score DESC
usort($matrix_data, function ($a, $b) {
    return $b['final_score'] <=> $a['final_score'];
});
?>
<title>Teacher Performance Matrix | RMPS</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .tp-container {
        font-family: 'Outfit', sans-serif;
        background-color: #f8fafc;
        padding: 24px;
        border-radius: 20px;
    }

    .tp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .tp-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tp-date {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .matrix-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        padding: 24px;
    }

    .matrix-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .matrix-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .phase-dropdown {
        border: 1px solid #cbd5e1;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        background-color: #fff;
        cursor: pointer;
        outline: none;
    }

    .phase-dropdown:focus {
        border-color: #3b82f6;
    }

    .tp-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tp-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 20px;
        text-align: left;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .tp-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #334155;
        vertical-align: middle;
    }

    .tp-table tr:last-child td {
        border-bottom: none;
    }

    .tp-table tr:hover td {
        background-color: #f8fafc;
    }

    .rank-cell {
        font-weight: 700;
        color: #64748b;
        text-align: center;
        width: 60px;
    }

    .teacher-col {
        font-weight: 600;
        color: #0f172a;
    }

    .badge-class-avg {
        display: inline-block;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 9999px;
        min-width: 55px;
        text-align: center;
    }

    .avg-green {
        background: #d1fae5;
        color: #065f46;
    }

    .avg-blue {
        background: #dbeafe;
        color: #1e40af;
    }

    .avg-orange {
        background: #fffbeb;
        color: #b45309;
    }

    .avg-red {
        background: #fee2e2;
        color: #991b1b;
    }

    .trend-text {
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
        color: #10b981;
    }

    .punctuality-cell {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #475569;
    }

    .final-score-capsule {
        display: inline-block;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 800;
        border-radius: 9999px;
        color: #fff;
        min-width: 45px;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .score-green {
        background: #10b981;
    }

    .score-blue {
        background: #3b82f6;
    }

    .score-orange {
        background: #f59e0b;
    }

    .score-red {
        background: #ef4444;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="tp-container">
            <div class="tp-header">
                <h1 class="tp-title">
                    <i class="fa fa-line-chart" style="color: #f59e0b;"></i>
                    Teacher Performance
                </h1>
                <div class="tp-date">
                    <i class="fa fa-calendar mr-1"></i>
                    <?= date('l, M j, Y') ?>
                </div>
            </div>

            <div class="matrix-card">
                <div class="matrix-header">
                    <h3 class="matrix-title">Teacher Performance Matrix</h3>
                    <select class="phase-dropdown">
                        <option>All Phases</option>
                        <option>Phase 1 (UT 1-7)</option>
                        <option>Phase 2 (UT 8-14)</option>
                        <option>Phase 3 (UT 15-21)</option>
                        <option>Phase 4 (UT 22-27)</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="tp-table">
                        <thead>
                            <tr>
                                <th width="8%" class="text-center">Rank</th>
                                <th width="22%">Teacher</th>
                                <th width="9%">Class</th>
                                <th width="12%">Subject</th>
                                <th width="11%">Class Avg %</th>
                                <th width="10%">Improvement</th>
                                <th width="14%">Low Performer Imp.</th>
                                <th width="10%">Punctuality</th>
                                <th width="9%">Final Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($matrix_data) > 0): ?>
                                <?php
                                $rank = 1;
                                foreach ($matrix_data as $row):
                                    // Color coding: 90-100% Green | 60-89% Blue | 40-59% Yellow | <40% Red
                                    $avg_val = $row['class_avg'];
                                    if ($avg_val >= 90) {
                                        $avg_class = 'avg-green';
                                    } else if ($avg_val >= 60) {
                                        $avg_class = 'avg-blue';
                                    } else if ($avg_val >= 40) {
                                        $avg_class = 'avg-orange';
                                    } else {
                                        $avg_class = 'avg-red';
                                    }

                                    // Same thresholds for final score
                                    $score_val = $row['final_score'];
                                    if ($score_val >= 90) {
                                        $score_class = 'score-green';
                                    } else if ($score_val >= 60) {
                                        $score_class = 'score-blue';
                                    } else if ($score_val >= 40) {
                                        $score_class = 'score-orange';
                                    } else {
                                        $score_class = 'score-red';
                                    }

                                    // Trophy visual markup
                                    $rank_display = $rank;
                                    if ($rank == 1) {
                                        $rank_display = '<i class="fa fa-trophy" style="color: #f59e0b; font-size: 16px;"></i>';
                                    } else if ($rank == 2) {
                                        $rank_display = '<i class="fa fa-trophy" style="color: #94a3b8; font-size: 16px;"></i>';
                                    } else if ($rank == 3) {
                                        $rank_display = '<i class="fa fa-trophy" style="color: #b45309; font-size: 16px;"></i>';
                                    }
                                    ?>
                                    <tr>
                                        <td class="rank-cell"><?= $rank_display ?></td>
                                        <td class="teacher-col"><?= htmlspecialchars($row['teacher']) ?></td>
                                        <td class="bold-col"><?= htmlspecialchars($row['class']) ?></td>
                                        <td><?= htmlspecialchars($row['subject']) ?></td>
                                        <td>
                                            <span class="badge-class-avg <?= $avg_class ?>"><?= $avg_val ?>%</span>
                                            <?php if (!$row['has_data']): ?>
                                                <small style="color:#94a3b8; display:block; font-size:10px;">No marks</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $imp = $row['improvement']; ?>
                                            <span class="trend-text" style="color: <?= $imp >= 0 ? '#10b981' : '#ef4444' ?>">
                                                <i class="fa fa-<?= $imp >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                                                <?= ($imp >= 0 ? '+' : '') . $imp ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="trend-text" style="color: <?= $row['low_perf_imp'] > 0 ? '#6366f1' : '#94a3b8' ?>">
                                                <i class="fa fa-users"></i>
                                                <?= $row['low_perf_imp'] ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="punctuality-cell" title="Days Late: <?= $row['days_late'] ?>">
                                                <i class="fa fa-clock-o" style="color: #6366f1;"></i>
                                                <?= $row['punctuality'] ?>%
                                                <?php if ($row['days_late'] > 0): ?>
                                                    <small style="color:#ef4444; font-size:10px;">(<?= $row['days_late'] ?>d late)</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="final-score-capsule <?= $score_class ?>"><?= $row['final_score'] ?></span>
                                        </td>
                                    </tr>
                                    <?php
                                    $rank++;
                                endforeach;
                                ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa fa-info-circle mb-2" style="font-size:32px;"></i>
                                        <p class="m-0">No teacher mappings or performance data found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>