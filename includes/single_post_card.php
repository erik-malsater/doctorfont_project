<?php
    $date = substr($post->fetched_post['date'], 0, -8);
?>

<div class="post_card single_post_card">
    
    <h2 class="post_card_title"><?=$post->fetched_post["title"];?></h2>
    <div class="post_card_image_frame">
        <img class="post_card_image" src="<?= $post->fetched_post['image'];?>" alt="Bild för inlägget <?=$post->fetched_post['title'];?>">
    </div>
    <div class="post_card_description">
        <?=$post->fetched_post["description"];?>
    </div>
    <p class="post_card_date"><?=$date;?></p>

    <hr class="post_card_border">

</div>