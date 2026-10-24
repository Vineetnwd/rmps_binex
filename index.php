<script>
    // Get the hostname of the current environment
    const hostname = window.location.hostname;

    // Define the URLs for local and production environments
    const localURL = 'http://localhost/binex/login.php';
    const productionURL = 'https://rmpublicschool.org/binex/login.php';
    // Redirect based on the detected environment
    if (hostname === 'localhost') {
        window.location = localURL; // Local environment
    } else {
        window.location = productionURL; // Production environment
    }
</script>

<h1> Please Wait </h1>

<?php echo phpinfo() ?>