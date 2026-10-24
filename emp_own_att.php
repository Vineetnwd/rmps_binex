<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  .calendar-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
  }

  .calendar {
    flex: 1;
    min-width: 300px;
    max-width: 600px;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-top: 10px;
  }

  .day-name, .day {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: center;
  }

  .day-name {
    font-weight: bold;
    background-color: #f0f0f0;
  }

  .present {
    background-color: #4caf50 !important;
    color: white;
  }

  .absent {
    background-color: #f44336 !important;
    color: white;
  }

  .leave {
    background-color: #ffeb3b !important;
    color: #000;
  }

  .sunday {
    background-color: #ff2d42 !important;
    color: white;
  }

  .today {
    background-color: #83ff87 !important;
  }

  .nav-btn {
    cursor: pointer;
    padding: 5px 15px;
    margin: 5px;
  }

  .stats-box {
    flex: 0 0 270px;
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    font-family: "Segoe UI", sans-serif;
  }

  .stats-title {
    text-align: center;
    margin-bottom: 15px;
    font-size: 18px;
    font-weight: 600;
    color: #444;
  }

  .stats-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 8px 0;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    color: #fff;
  }

  .stats-card i {
    margin-right: 8px;
  }

  .stats-card.present {
    background-color: #4caf50;
  }

  .stats-card.absent {
    background-color: #f44336;
  }

  .stats-card.leave {
    background-color: #ffeb3b;
    color: #000;
  }

  .stats-card.sunday {
    background-color: #ff7043;
  }

  .stats-card.total {
    background-color: #607d8b;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Own Attendance Report</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item active">Own Attendance Report</li>
    </ol>
  </section>

  <section class="content">
    <div class="box box-default">
      <?php 
        $emp_info = get_data('employee', strtoupper($_SESSION['user_name']), null, 'emp_code')['data'];
        $sql = "SELECT * FROM employee_att WHERE emp_id = {$emp_info['id']}";
        $res = direct_sql($sql, 'get', false)['data'];
      ?>

      <script>
        const attendanceData = <?php echo json_encode($res); ?>;
        const attendanceMap = {};

        attendanceData.forEach(entry => {
          const monthKey = entry.att_month;
          attendanceMap[monthKey] = entry;
        });

        function getMonthKey(date) {
          const month = date.toLocaleString("default", { month: "short" }).toLowerCase();
          const year = date.getFullYear();
          return `${month}_${year}`;
        }
      </script>

      <div class="box-body">
        <div class="calendar-container">
          <!-- Calendar -->
          <div class="calendar">
            <div class="header">
              <button class="nav-btn" onclick="prevMonth()">&lt; Prev</button>
              <h2 id="monthYear"></h2>
              <button class="nav-btn" onclick="nextMonth()">Next &gt;</button>
            </div>

            <div class="days" id="dayNames">
              <div class="day-name">Sun</div>
              <div class="day-name">Mon</div>
              <div class="day-name">Tue</div>
              <div class="day-name">Wed</div>
              <div class="day-name">Thu</div>
              <div class="day-name">Fri</div>
              <div class="day-name">Sat</div>
            </div>
            <div class="days" id="calendarDays"></div>
          </div>

          <!-- Stats Panel -->
          <div class="stats-box">
            <h4 class="stats-title">📊 Attendance Summary</h4>
            <div class="stats-card present">
              <i class="fa fa-check-circle"></i> Present
              <span id="statPresent">0</span>
            </div>
            <div class="stats-card absent">
              <i class="fa fa-times-circle"></i> Absent
              <span id="statAbsent">0</span>
            </div>
            <div class="stats-card leave">
              <i class="fa fa-plane-departure"></i> Leave
              <span id="statLeave">0</span>
            </div>
            <div class="stats-card sunday">
              <i class="fa fa-calendar-day"></i> Sundays
              <span id="statSunday">0</span>
            </div>
            <div class="stats-card total">
              <i class="fa fa-calendar-alt"></i> Total Days
              <span id="statTotal">0</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

<script>
  const calendarDays = document.getElementById("calendarDays");
  const monthYear = document.getElementById("monthYear");

  let currentDate = new Date();

  function renderCalendar(date) {
    const today = new Date();
    const year = date.getFullYear();
    const month = date.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();

    const monthKey = getMonthKey(date);
    const attData = attendanceMap[monthKey] || {};

    monthYear.textContent = date.toLocaleString("default", { month: "long", year: "numeric" });
    calendarDays.innerHTML = "";

    let presentCount = 0;
    let absentCount = 0;
    let leaveCount = 0;
    let sundayCount = 0;

    for (let i = 0; i < firstDay; i++) {
      calendarDays.innerHTML += `<div class="day"></div>`;
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const dateObj = new Date(year, month, day);
      const isSunday = dateObj.getDay() === 0;
      const isToday = dateObj.toDateString() === today.toDateString();
      const key = `d_${day}`;
      const status = attData[key] || '';

      const dayEl = document.createElement("div");
      dayEl.className = "day";
      dayEl.textContent = day;

      if (status === "P") {
        dayEl.classList.add("present");
        presentCount++;
      } else if (status === "A") {
        dayEl.classList.add("absent");
        absentCount++;
      } else if (status === "L") {
        dayEl.classList.add("leave");
        leaveCount++;
      } else if (isSunday) {
        dayEl.classList.add("sunday");
        sundayCount++;
      } else if (isToday) {
        dayEl.classList.add("today");
      }

      calendarDays.appendChild(dayEl);
    }

    // Update stats
    document.getElementById("statPresent").textContent = presentCount;
    document.getElementById("statAbsent").textContent = absentCount;
    document.getElementById("statLeave").textContent = leaveCount;
    document.getElementById("statSunday").textContent = sundayCount;
    document.getElementById("statTotal").textContent = daysInMonth;
  }

  function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  }

  function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  }

  renderCalendar(currentDate);
</script>

<?php require_once('system/footer2.php'); ?>
