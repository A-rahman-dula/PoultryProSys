<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'checkLogin.php';
require_once 'admin_frame.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM user WHERE role = :role";
$stmt = $db->prepare($query);
$role = 'customer';
$stmt->bindParam(':role', $role, PDO::PARAM_STR);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_id = $_SESSION['user_id'];

$admin = CheckLogin::checkLoginAndRole($user_id, 'admin');

$adminframe = new AdminFrame();
$adminframe->first_part($admin);
?>

<div class="contentArea">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>CUSTOMERS</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <!--<th scope="col">UserID</th>-->
                                    <th scope="col">Name</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">City</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Registered</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $uid = 1;
                                foreach ($users as $user) {
                                ?>
                                    <tr>
                                        <td><?php echo $uid; ?></td>
                                        <!--<td><?php echo htmlspecialchars($user['user_id']); ?></td>-->
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                                        <td><?php echo htmlspecialchars($user['city']); ?></td>
                                        <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['CREATED_AT']); ?></td>
                                        <td>
                                            <?php if ($user['status'] == 0) { ?>
                                                <button class="btn btn-success">
                                                    <a href="unblock_user.php?unblock=<?php echo urlencode($user['user_id']); ?>&role=customer" class="text-light">Unblock</a>
                                                </button>
                                            <?php } else { ?>
                                                <button class="btn btn-danger">
                                                    <a href="block_user.php?block=<?php echo urlencode($user['user_id']); ?>&role=customer" class="text-light">Block</a>
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                    $uid++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$adminframe->last_part();
?>
