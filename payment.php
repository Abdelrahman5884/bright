<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$conn = Database::getInstance()->getConnection();

$stmt = $conn->prepare("SELECT price FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    echo "<script>alert('Course not found!'); window.location.href='courses.php';</script>";
    exit;
}

$course_price = number_format($course['price'], 2);

$stmt = $conn->prepare("SELECT amount_paid, paid_full FROM payments WHERE user_id = ? AND course_id = ?");
$stmt->execute([$user_id, $course_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

$amount_paid = $payment ? $payment['amount_paid'] : 0;
$paid_full = $payment ? $payment['paid_full'] : 0;
$remaining = $course_price - $amount_paid;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = (int) $_POST["amount"];

    if ($amount <= 0) {
        echo "<script>alert('Enter a valid amount!'); window.location.href='payment.php?course_id=" . $course_id . "';</script>";
        exit;
    }

    $new_amount_paid = $amount_paid + $amount;
    $paid_full = $new_amount_paid >= $course_price ? 1 : 0;

    if ($payment) {
        $stmt = $conn->prepare("UPDATE payments SET amount_paid = ?, paid_full = ? WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$new_amount_paid, $paid_full, $user_id, $course_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO payments (user_id, course_id, amount_paid, paid_full) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $course_id, $new_amount_paid, $paid_full]);
    }

    echo "<script>alert('Payment successful!'); window.location.href='payment.php?course_id=" . $course_id . "';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vodafone Cash Payment</title>
    <style>
    body { text-align: center; font-family: Arial, sans-serif; background: #f4f4f4; padding: 50px; }
    .container { background: white; padding: 20px; border-radius: 10px; width: 350px; margin: auto; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); border: 3px solid #e60000; }
    .btn { background: #e60000; color: white; padding: 10px; border: none; cursor: pointer; width: 100%; border-radius: 5px; }
    .btn:hover { background: #b30000; }
    </style>
</head>
<body>

<div class="container">
    <h2>Vodafone Cash Payment</h2>
    <p>Course Price: <strong>EGP <?php echo $course_price; ?></strong></p>
    <p>Amount Paid: <strong>EGP <?php echo $amount_paid; ?></strong></p>

    <?php if (!$paid_full) { ?>
        <p>Remaining Amount: <strong>EGP <?php echo $remaining; ?></strong></p>
        <p>Dial this USSD code on your phone:</p>
        <h3 style="background: #eee; padding: 10px; border-radius: 5px;">9*7*01004732940*<?php echo $remaining; ?>#</h3>

        <form action="" method="post">
            <input type="number" name="amount" placeholder="Enter amount" required>
            <button type="submit" class="btn">Pay Now</button>
        </form>
    <?php } else { ?>
        <p>Full payment received! Access your course now.</p>
        <a href="watch-video.php?course_id=<?= $course_id ?>"><button class="btn">Watch Course</button></a>
    <?php } ?>
</div>

</body>
</html>
