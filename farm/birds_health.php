<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

require_once '../classes/config.php';
require_once '../classes/checkLogin.php';
require_once 'Frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?msg=Please Login before Proceeding");
    exit();
}

$user_id = $_SESSION["user_id"];
$database = new Database();
$con = $database->getConnection();
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');
$frame = new Frame();
$frame->first_part($farm);

try {
    // Fetch all batches for the dropdown
    $query = "SELECT batch_id, batch, bird_type, quantity FROM birds WHERE user_id = :user_id";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize variables for chart data
    $totalGoodHealth = 0;
    $totalIllness = 0;
    $totalDeaths = 0;

    if (isset($_GET['batch_id'])) {
        $selected_batch_id = filter_input(INPUT_GET, 'batch_id', FILTER_VALIDATE_INT);

        if ($selected_batch_id !== false) {
            // Fetch the selected batch's health status
            $query = "SELECT 
                        b.quantity AS total_birds, 
                        COALESCE(SUM(h.no_illness), 0) AS total_illness, 
                        COALESCE(SUM(h.no_deaths), 0) AS total_deaths
                      FROM 
                        birds b 
                      LEFT JOIN 
                        health_status h 
                      ON 
                        b.batch_id = h.batch_id 
                      WHERE 
                        b.batch_id = :batch_id
                      GROUP BY 
                        b.quantity";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':batch_id', $selected_batch_id);
            $stmt->execute();
            $selectedBatch = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($selectedBatch) {
                $totalGoodHealth = $selectedBatch['total_birds'] - ($selectedBatch['total_illness'] + $selectedBatch['total_deaths']);
                $totalIllness = $selectedBatch['total_illness'];
                $totalDeaths = $selectedBatch['total_deaths'];
            }
        } else {
            $error_message = "Invalid batch ID.";
        }
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $error_message = "An unexpected error occurred. Please try again later.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $batch_id = $_POST['batch_id'];
    $no_illness = $_POST['no_illness'];
    $no_deaths = $_POST['no_deaths'];
    $description = $_POST['description'];

    try {
        $query = $con->prepare('INSERT INTO health_status (user_id, batch_id, no_illness, no_deaths, description) VALUES (:user_id, :batch_id, :no_illness, :no_deaths, :description)');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':batch_id', $batch_id);
        $query->bindParam(':no_illness', $no_illness);
        $query->bindParam(':no_deaths', $no_deaths);
        $query->bindParam(':description', $description);

        if ($query->execute()) {
            echo "<script>window.location.href = 'birds_health.php?batch_id=$batch_id';</script>";
            exit();
        } else {
            $error_message = "Failed to add bird health status.";
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        $error_message = "An unexpected error occurred. Please try again later.";
    }
}
?>
<main class="col-lg-10 col-md-9 col-sm-12 p-0 vh-100 overflow-auto">
    <div class="container">
        <div class="row my-4 mx-2 py-4">

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <div class="card">
                    <div class="card-header p-3 text-center" style="background-color: #9B59B6;">
                        <h5 class="card-title text-white"><strong style="font-size: 24px;">Birds Health Details</strong></h5>
                    </div>
                    <div class="card-body" style="background-color: #D4C8DE;">

                        <?php if (isset($success_message)) : ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <input type="hidden" class="form-control" value="<?php echo $user_id; ?>" name="user_id" id="user_id">


                            <div class="row p-2 mt-3">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Batch:</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="batch_id" id="batch_id" required onchange="location = 'birds_health.php?batch_id=' + encodeURIComponent(this.value);">
                                                <option value="">Select Batch</option>
                                                <?php foreach ($batches as $batch) : ?>
                                                    <option value="<?php echo htmlspecialchars($batch['batch_id']); ?>"
                                                            <?php if (isset($selected_batch_id) && $selected_batch_id == $batch['batch_id']) echo 'selected'; ?>>
                                                                <?php echo htmlspecialchars($batch['batch']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">No of ill birds:</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="number" name="no_illness" id="no_illness" required>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-1">
                                        <label class="col-sm-4 col-form-label">No of death birds:</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" type="number" name="no_deaths" id="no_deaths" required>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row p-2">
                                <div class="col">
                                    <div class="row mb-1">
                                        <label class="col-sm-4 col-form-label">Description:</label>
                                        <div class="col-sm-12">
                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row px-3" style="text-align:center;">
                                <input type="submit" class="btn btn-primary" name="submit" value="Add">
                            </div>

                            <div class="row px-3 mt-2" style="text-align:center;">
                                <a href="birds.php" class="btn btn-danger">Back</a>
                            </div>



                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-10 col-12 mb-3 px-5">
                <canvas id="birdHealthChart" style="width:100%;max-width:600px"></canvas>
            </div>

        </div>

    </div>
</main>

<script>
    var totalGoodHealth = <?php echo $totalGoodHealth; ?>;
    var totalIllness = <?php echo $totalIllness; ?>;
    var totalDeaths = <?php echo $totalDeaths; ?>;

    var ctx = document.getElementById('birdHealthChart').getContext('2d');

    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Healthy', 'Ill', 'Dead'],
            datasets: [{
                    label: 'Bird Health Status',
                    data: [totalGoodHealth, totalIllness, totalDeaths],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 1
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Bird Health Status Summary',
                fontSize: 20
            }
        }
    });
</script>


<?php
$frame->last_part();
?>