<?php 
require "header.php";
require "upload.php";

?>

<!-- <iframe src="https://www.youtube.com/embed/rfscVS0vtbw?" width="100%" height="auto" frameborder="0" allowfullscreen style="max-width: 100%;aspect-ratio: 16 / 9;"></iframe> -->
<section class="watch-video">
   <div class="video-container">
      <div class="video">
       <video  src="<?php echo $video_url ?>" controls poster="<?php echo $video_url ?>" id="video">
       </video>
      </div>
      <h3 class="title"><?php echo $describtion  ?></h3>
      <div class="tutor">
         <img src="<?php echo $userImage ?>" alt="">
         <div>
            <h3><?php echo $userName  ?></h3>
            <span>developer</span>
         </div>
      </div>
      <form action="" method="post" class="flex">
         <a href="playlist.php" class="inline-btn">view playlist</a>
         <button><i class="far fa-heart"></i><span>like</span></button>
      </form>
   </div>

</section>

<?php require "footer.php";  ?>
