<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

if (empty($_SESSION['signed_in'])) {
    exit('please sign in');
}

$get_title = $dbc->prepare('SELECT * FROM titles WHERE title_id = ? LIMIT 1');
$get_title->bind_param('i', $title_id);
$get_title->execute();
$title_result = $get_title->get_result();

if ($title_result->num_rows == 0) {
    exit("Could not find community");
}

$title = $title_result->fetch_array();

if ($_SESSION['user_id'] != $title['title_by']) {
    exit('you didn\'t make the community faggot');
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $tabTitle = 'Cedar - Edit Community';
    printHeader('');

    echo '<div id="sidebar">
	  <section class="sidebar-container" id="sidebar-community">
	    <span id="sidebar-cover">
	      <a href="/titles/'. $title['title_id'] .'">
	        <img src="'. $title['title_banner'] .'">
	      </a>
	    </span>
	    <header id="sidebar-community-body">
	    <span id="sidebar-community-img">
	      <span class="icon-container">
	    	<a href="/titles/'. $title['title_id'] .'">
	    	  <img src="'. $title['title_icon'] .'" id="icon">
	    	</a>
	      </span>
        <span class="platform-tag">';

    switch ($title['type']) {
        case 1:
            echo '<img src="/assets/img/platform-tag-wiiu.png">';
            break;
        case 2:
            echo '<img src="/assets/img/platform-tag-3ds.png">';
            break;
        case 3:
            echo '<img src="/assets/img/platform-tag-wiiu-3ds.png">';
            break;
        case 4:
            echo '<img src="/assets/img/platform-tag-switch.png">';
            break;
    }

    echo '</span>
    </span>
    '. ($title['type'] == 5 ? '<span class="news-community-badge">Announcement Community</span>' : '') .'
		'. ($title['user_made'] == 1 ? '<span class="news-community-badge">User-Created Community</span>' : '') . ($title['owner_only'] == 1 ? '<span class="news-community-badge">Private
</span>' : '') .'
    <h1 class="community-name"><a href="/titles/'. $title['title_id'] .'">'. htmlspecialchars($title['title_name'], ENT_QUOTES) .'</a></h1>
    </header>
      <div class="community-description js-community-description">
		<p class="text js-truncated-text">'. nl2br(htmlspecialchars($title['title_desc'], ENT_QUOTES)) .'</p>';
    if (!empty($title['title_by'])) {
        $get_title_owner = $dbc->prepare('SELECT * FROM users WHERE user_id = ?');
        $get_title_owner->bind_param('i', $title['title_by']);
        $get_title_owner->execute();
        $title_owner_result = $get_title_owner->get_result();
        $title_owner = $title_owner_result->fetch_array();
        echo '<p style="text-align:  center;">Community owner: <a href="/users/'. htmlspecialchars($title_owner['user_name'], ENT_QUOTES) .'/posts">'. $title_owner['user_name'] .'</a></p>';
    }
    echo '</div><div id="edit-title"><a class="button symbol" href="/titles/'. $title['title_id'] .'/edit">Community Settings</a></div><button type="button" class="symbol button favorite-button';

    $check_favorite = $dbc->prepare('SELECT * FROM favorite_titles WHERE user_id = ? AND title_id = ?');
    $check_favorite->bind_param('ii', $_SESSION['user_id'], $title['title_id']);
    $check_favorite->execute();
    $favorite_result = $check_favorite->get_result();

    if (!$favorite_result->num_rows == 0) {
        echo ' checked ';
    }

    echo '"data-title-id="'. $title['title_id'] .'"><span class="favorite-button-text">Favorite</span></button>';

    echo '<div class="sidebar-setting"><div class="sidebar-post-menu"></div></div></section></div>'; ?>

    <div class="main-column">
        <div class="post-list-outline">
            <h2 class="label">Edit Community</h2>
            <form id="account-settings-form" class="setting-form community-creation" method="post" action="/titles/<?php echo $title['title_id'] ?>/edit">
                <ul class="settings-list">
                    <li>
                        <p class="settings-label">Name your community.</p>
                        <input class="textarea" value="<?php echo $title['title_name'] ?>" placeholder="Your community name." type="text" maxlength="64" name="name" style="cursor: auto; height: auto;">
                    </li>

                    <li>
                        <p class="settings-label">Write a description for your community.</p>
                        <textarea class="textarea" name="description" maxlength="400" placeholder="Your community description."><?php echo $title['title_desc'] ?></textarea>
                    </li>

                    <li>
                        <p class="settings-label">Upload an icon for your community.</p>
                        <p class="note">Make sure the image isn't too big. Also make sure it's square shaped!<br>
                        The recommended resolution is 128x128px.</p>
                        <input type="file" name="title_icon" accept="image/*">
                    </li>

                    <li>
                        <p class="settings-label">Upload a banner for your community.</p>
                        <p class="note">Again, make sure the image isn't too big. The recommended resolution is 347x145px.</p>
                        <input type="file" name="title_banner" accept="image/*">
                    </li>
                    <li>
                        <p class="settings-label">Private community.</p>
                        <p class="note">If your community is private only you will be able to post, but anyone can see your posts and comment.</p>
                        <div class="select-content">
                            <div class="select-button">
                                <select name="is_private">
                                    <option value="0">Public</option>
                                    <option value="1" <?php echo($title['owner_only'] ? 'selected' : '') ?> >Private</option>
                                </select>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="form-buttons"><input type="submit" class="black-button apply-button" value="Create Community"></div>
            </form>
        </div>
    </div>
</div>
</div>

<?php
} else {
        $error = false;
    if (empty($_POST['name'])) {
        $error = 'Please write a name for your community.';
    }
    if (empty($_POST['description'])) {
        $error = 'Please write a description.';
    }

        $check_titles = $dbc->prepare('SELECT * FROM titles WHERE title_name = ? AND title_id != ? LIMIT 1');
        $check_titles->bind_param('si', $_POST['name'], $title['title_id']);
        $check_titles->execute();
        $titles_result = $check_titles->get_result();

    if (!$titles_result->num_rows == 0) {
        $error = 'A community with this name already exists.';
    }

    if (empty($error)) {
        $img = $_FILES['title_icon'];
        if (empty($img['name'])) {
            $error = 'Please upload an icon.';
        } else {
            $filename = $img['tmp_name'];
            $icon = uploadImage($filename, 128, 128);
            if ($icon == 1) {
                $error = 'Image upload failed.';
            }
        }

        $img2=$_FILES['title_banner'];
        if (empty($img2['name'])) {
            $error = 'Please upload a banner.';
        } else {
            $filename = $img2['tmp_name'];
            $banner = uploadImage($filename, 347, 145);
            if ($banner == 1) {
                $error = 'Image upload failed.';
            }
        }

        if (empty($error)) {
            $edit_community = $dbc->prepare('UPDATE titles SET title_name = ?, title_desc = ?, title_icon = ?, title_banner = ?, owner_only = ? WHERE title_id = ?');
            $edit_community->bind_param('ssssii', $_POST['name'], $_POST['description'], $icon, $banner, $_POST['is_private'], $title['title_id']);
            $edit_community->execute();
            echo 'Community edited.';
        } else {
            echo $error;
        }
    } else {
        echo $error;
    }
}
