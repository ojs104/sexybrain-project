<?php
include "../connect/connect.php";
include "../connect/session.php";

if (!isset($_SESSION['youId'])) {
    echo "<script>alert('로그인 후 이용해주세요.'); location.href='login.php';</script>";
}

$commentId = $_POST['commentId'];
$boardId = $_POST['boardId'];
$msg = $_POST['msg'];
$youId = $_SESSION['memberId'];

// 댓글 작성자 ID 가져오기
$sql = "SELECT memberId FROM SBComment WHERE commentId = ? AND boardId = ?";
$stmt = $connect->prepare($sql);
if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($connect->error));
}
$stmt->bind_param("ii", $commentId, $boardId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);
$commentAuthorId = $row['memberId'];

// 댓글 작성자와 현재 로그인한 사용자가 동일한지 확인
if ($youId != $commentAuthorId) {
    echo "youId: $youId, commentAuthorId: $commentAuthorId"; // 디버깅을 위한 출력
    echo "자신이 작성한 댓글만 수정할 수 있습니다.";
    exit;
}

$msg = $connect->real_escape_string($msg); // SQL Injection 방지

$sql = "UPDATE SBComment SET commentMsg = ? WHERE commentId = ? AND boardId = ?";
$stmt = $connect->prepare($sql);
if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($connect->error));
}
$stmt->bind_param("sii", $msg, $commentId, $boardId);
$result = $stmt->execute();

if ($result) {
    echo "댓글이 수정되었습니다.";
} else {
    echo "댓글 수정에 실패했습니다. 오류: " . $connect->error;
}
?>