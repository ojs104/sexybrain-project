<?php
include "../connect/connect.php";
include "../connect/session.php";

// 검색된 결과를 저장할 변수
$searchedResults = "";

if (isset($_POST['searchKeyword'])) {
    $searchKeyword = $_POST['searchKeyword'];

    $searchSql = "SELECT * FROM sexyBoard 
                WHERE boardDelete = 1 AND (boardTitle LIKE '%$searchKeyword%' OR boardContents LIKE '%$searchKeyword%') 
                ORDER BY boardId DESC";

    $searchResult = $connect->query($searchSql);
    if ($searchResult->num_rows > 0) {
        while ($post = $searchResult->fetch_assoc()) {
            $searchedResults .= "<div class='card'>";
            
            $searchedResults .= "<figure class='card__img'>";
            $searchedResults .= "<a href='boardView.php?boardId=" . $post['boardId'] . "'>";
            $searchedResults .= "<img src='../assets/board/" . $post['boardImgFile'] . "' alt='" . $post['boardTitle'] . "'>";
            $searchedResults .= "</a>";
            $searchedResults .= "</figure>";
            
            $searchedResults .= "<div class='card__text'>";
            
            $searchedResults .= "<h4>" . $post['boardCategory'] . "</h4>"; // 카테고리
            $searchedResults .= "<h4>" . $post['boardTitle'] . "</h4>"; // 타이틀
            $searchedResults .= "<div class='card__desc'>";
            $searchedResults .= "<h4>작성자: " . $post['boardAuthor'] . "</h4><h4>컨텐츠: " . $post['boardContents'] . "</h4><h4>조회수: " . $post['boardView'] . "</h4>";
            $searchedResults .= "</div>";
            
            $searchedResults .= "</div></div>"; // 각 항목을 분리
        }
    } else {
        $searchedResults = "검색 결과가 없습니다.";
    }
}


?>
 
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판 목록</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/boardHome.css">

</head>
<body>
    <div class="wrap">
    <?php include "../include/header.php" ?>

        <!-- //header -->

        <main>
            <header class="intro__box">
                <h2>
                    뇌섹남녀 커뮤니티에서<br>
                    다양한 이야기를 나누세요
                </h2>
                <p>공지사항과 질문 외에도 자유롭게 이야기를 나눌 수 있습니다.</p>
                <a href="boardWrite.php">글쓰기</a>
            </header>
            <!-- //intro__box -->

            <section class="board__wrap container">
                <div class="board__inner">
                    <div class="card__inner column4">
                        <legend class="blind">게시판 검색</legend>        
                        <form method="post" action="search_results.php">
                            <input type="text" name="searchKeyword" id="searchKeyword" placeholder="검색어를 입력하세요" required>
                            <!-- <button type="submit">검색</button> -->
                        </form>

                        <?php 
                        if (isset($searchResult)) {
                            foreach ($searchResult as $result) { 
                        ?>
                            <!-- 여기서 검색된 결과를 출력합니다 -->
                           
                        <?php 
                            } 
                        } 
                        ?>
<?php echo $searchedResults; ?>
                        <!-- //card01 -->
    
                    </div>
                        <!-- //board__inner -->    
                        <div class="board__pages">
                            <ul>
<?php if ($page > 1) { ?>  
    <li><a href="board.php?page=<?= $page - 1 ?>">&lt;</a></li>
<?php } else { ?>
    <li style="display: none;">&lt;</li>
<?php } ?>      

    <li class="active"><a href="board.php?page=1">1</a></li> <!-- 1 페이지에 대한 페이지 번호 추가 -->

<?php if ($page === 2) { ?>
    <li class="active"><a href="board.php?page=2">2</a></li>
<?php } ?>

<?php if ($page === 3) { ?>
    <li class="active"><a href="board.php?page=3">3</a></li>
<?php } ?>

<?php if ($page === 4) { ?>
    <li class="active"><a href="board.php?page=4">4</a></li>
<?php } ?>

<?php if ($page === 5) { ?>
    <li class="active"><a href="board.php?page=5">5</a></li>
<?php } ?>

<?php if ($page === 6) { ?>
    <li class="active"><a href="board.php?page=6">6</a></li>
<?php } ?>

<?php if ($page < $totalPages) { ?>
    <li><a href="board.php?page=<?= $page + 1 ?>">></a></li>
<?php } else { ?>
    <li><a href="#" onclick="alert('마지막 페이지 입니다')">></a></li>
<?php } ?>

                         </ul>
</div>                  
                </div>
                    <!-- //board__pages -->
               

            <?php include "boardCategory.php" ?>
            </section>
            <!-- //board__wrap -->
        </main>
        <!-- //main -->
        <?php include "../include/footer.php" ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      
    </script>

</body>
</html>