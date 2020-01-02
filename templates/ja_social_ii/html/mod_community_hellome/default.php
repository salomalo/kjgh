<?php
/**
* @copyright (C) 2015 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$svgPath = CFactory::getPath('template://assets/icon/joms-icon.svg');
include_once $svgPath;

?>

<div id="cModule-HelloMe" class="joms-module joms-centered <?php echo $params->get('moduleclass_sfx'); ?>">

<?php if($user->id){

$userParams = $user->getParams();
$config = CFactory::getConfig();
$my = CFactory::getUser();
$url = CRoute::_('index.php?option=com_community');
// $isMine = COwnerHelper::isMine($my->id, $user->id);
// $isFriend = CFriendsHelper::isConnected($user->id, $my->id) && $user->id != $my->id;
// $isWaitingApproval = CFriendsHelper::isWaitingApproval($my->id, $user->id);
// $isWaitingResponse = CFriendsHelper::isWaitingApproval($user->id, $my->id);
// $isBlocked = $user->isBlocked();

//links information
$photoEnabled = ($config->get('enablephotos')) ? true : false;
$eventEnabled = ($config->get('enableevents')) ? true : false;
$groupEnabled = ($config->get('enablegroups')) ? true : false;
$videoEnabled = ($config->get('enablevideos')) ? true : false;


//likes
// CFactory::load('libraries', 'like');
// $like = new Clike();
// $isLikeEnabled = $like->enabled('profile') && $userParams->get('profileLikes', 1) ? 1 : 0;
// $isUserLiked = $like->userLiked('profile', $user->id, $my->id);
// /* likes count */
// $likes = $like->getLikeCount('profile', $user->id);

$profileFields = '';
$themeModel = CFactory::getModel('theme');
$profileModel = CFactory::getModel('profile');
$settings = $themeModel->getSettings('profile');

$profile = $profileModel->getViewableProfile($user->id, $user->getProfileType());
$profile = JArrayHelper::toObject($profile);

$groupmodel = CFactory::getModel('groups');
$profile->_groups = $groupmodel->getGroupsCount($profile->id);

$eventmodel = CFactory::getModel('events');
$profile->_events = $eventmodel->getEventsCount($profile->id);

$profile->_friends = $user->_friendcount;

$videoModel = CFactory::getModel('Videos');
$profile->_videos = $videoModel->getVideosCount($profile->id);

$photosModel = CFactory::getModel('photos');
$profile->_photos = $photosModel->getPhotosCount($profile->id);
?>

<div class="joms-module--hellome">

    <div class="joms-hcard">

        <?php if($moduleParams->get('show_badge')) { ?>
	<div class="joms-hcard__badges">
            <img src="<?php echo $badge->current->image;?>" alt="<?php echo $badge->current->title;?>" class="joms-focus__badges"/>
	</div>
        <?php } ?>

        <div class="joms-hcard__cover">

            <img src="<?php echo $user->getCover(); ?>" alt="<?php echo $user->getDisplayName(); ?>" style="width:100%;top:<?php echo $userParams->get('coverPosition', ''); ?>">

            <?php if($moduleParams->get('show_avatar') || $moduleParams->get('show_name')) { ?>

                <div class="joms-hcard__info">
                    <?php if($moduleParams->get('show_avatar')){ ?>
                    <div class="joms-avatar">
                        <a href="<?php echo CUrlHelper::userLink($user->id); ?>"><img src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>"></a>
                    </div>
                    <?php } ?>
                    <?php if($moduleParams->get('show_name')){ ?>
                    <div class="joms-hcard__info-content">
                        <h3 class="reset-gap"><?php echo $user->getDisplayName(); ?></h3>
                        <div class="joms-gap--small"></div>
                    </div>
                    <?php } ?>
                </div>

           <?php } ?>

        </div>
    </div>


    <div class="joms-action--hellome">

    <?php if($moduleParams->get('show_notifications')){ ?>

        <div>
            <a class="joms-button--hellome" title="<?php echo JText::_('COM_COMMUNITY_NOTIFICATIONS_GLOBAL'); ?>"
                    href="javascript:"
                    onclick="joms.popup.notification.global();">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-earth"></use>
                </svg>
                <span><small class="joms-js--notiflabel-general"><?php echo ($newEventInviteCount) ? $newEventInviteCount : ''; ?></small></span>
            </a>
        </div>
        <div>
            <a class="joms-button--hellome" title="<?php echo JText::_('COM_COMMUNITY_NOTIFICATIONS_INVITE_FRIENDS'); ?>"
                    href="<?php echo CRoute::_('index.php?option=com_community&view=friends&task=pending'); ?>"
                    onclick="joms.popup.notification.friend(); return false;">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-users"></use>
                </svg>
                <span><small class="joms-js--notiflabel-frequest"><?php echo ($newFriendInviteCount) ? $newFriendInviteCount : ''; ?></small></span>
            </a>
        </div>
        <div>
            <a class="joms-button--hellome" title="<?php echo JText::_('COM_COMMUNITY_NOTIFICATIONS_INBOX'); ?>"
                    href="<?php echo CRoute::_('index.php?option=com_community&view=chat'); ?>"
                    onclick="joms.popup.notification.chat(this); return false;">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-envelope"></use>
                </svg>
                <span><small class="joms-js--notiflabel-chat"><?php echo ($newChatCount) ? $newChatCount : ''; ?></small></span>
            </a>
            <ul class="joms-popover joms-arrow--top joms-popover--toolbar-chat">
                <li class="joms-js--empty" style="display:block">
                    <span><?php echo JText::_('COM_COMMUNITY_CHAT_NOTIF_NO_NEW_MESSAGE') ?></span>
                </li>
                <div>
                    <a href="<?php echo CRoute::_('index.php?option=com_community&view=chat'); ?>" class="joms-button--neutral joms-button--full"><?php echo JText::_('COM_COMMUNITY_CHAT_NOTIF_SHOW_ALL') ?></a>
                </div>
            </ul>
        </div>

    <?php } ?>

        <?php if($params->get('show_logout',1)){ ?>
        <div>
            <a href="javascript:void(0);" onclick="document.getElementById('js-hellome-logout-form').submit();" class="joms-button--hellome logout">
                <svg viewBox="0 0 16 16" class="joms-icon joms-icon--white">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-switch"></use>
                </svg>
            </a>
        </div>
        <form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="js-hellome-logout-form" style="display: none;">
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.logout" />
            <input type="hidden" name="return" value="<?php echo $logoutLink; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
        <?php } ?>
    </div>

    <?php if($moduleParams->get('show_menu')) { ?>
        <ul class="joms-list joms-list--hellome">
            <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=friends'); ?>"><?php echo JText::_('MOD_HELLOME_MY_FRIENDS'); ?><span><?php echo $user->_friendcount; ?></span></a></li>

            <?php if($photoEnabled) { ?>
                <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos'); ?>"><?php echo JText::_('MOD_HELLOME_MY_PHOTOS'); ?><span><?php echo $totalPhotos; ?></span></a></li>
            <?php } ?>

            <?php if($videoEnabled) { ?>
                <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=myvideos'); ?>"><?php echo JText::_('MOD_HELLOME_MY_VIDEOS'); ?><span><?php echo $totalVideos; ?></span></a></li>
            <?php } ?>

            <?php if($groupEnabled) { ?>
                <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=mygroups'); ?>"><?php echo JText::_('MOD_HELLOME_MY_GROUPS'); ?><span><?php echo $totalGroups; ?></span></a></li>
            <?php } ?>

            <?php if($eventEnabled) { ?>
                <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=events&task=myevents'); ?>"><?php echo JText::_('MOD_HELLOME_MY_EVENTS'); ?><span><?php echo $totalEvents; ?></span></a></li>
            <?php } ?>
        </ul>
    <?php } ?>

</div>

<?php }else{

$config = CFactory::getConfig();
$usersConfig = JComponentHelper::getParams('com_users');
$fbHtml = '';

if ($config->get('fbconnectkey') && $config->get('fbconnectsecret') && !$config->get('usejfbc')) {
    $facebook = new CFacebook();
    $fbHtml = $facebook->getLoginHTML();
}

if ($config->get('usejfbc')) {
    if (class_exists('JFBCFactory')) {
       $providers = JFBCFactory::getAllProviders();
       $fbHtml = '';
       foreach($providers as $p){
            $fbHtml .= $p->loginButton();
       }
    }
}

?>

<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="498" height="144" viewBox="0 0 498 144" class="joms-hide">
    <defs>
        <g id="joms-icon-user">
            <path class="path1" d="M9.732 10.98c-0.345-0.055-0.353-1.005-0.353-1.005s1.015-1.005 1.236-2.356c0.595 0 0.963-1.437 0.368-1.942 0.025-0.532 0.765-4.177-2.982-4.177-3.747 0-3.007 3.645-2.982 4.177-0.595 0.505-0.228 1.942 0.368 1.942 0.221 1.351 1.236 2.356 1.236 2.356s-0.008 0.95-0.353 1.005c-1.113 0.177-5.268 2.010-5.268 4.020h14c0-2.010-4.155-3.843-5.268-4.020z"></path>
        </g>
        <g id="joms-icon-key">
            <path class="path1" d="M11 0c-2.761 0-5 2.239-5 5 0 0.313 0.029 0.619 0.084 0.916l-6.084 6.084v3c0 0.552 0.448 1 1 1h1v-1h2v-2h2v-2h2l1.298-1.298c0.531 0.192 1.105 0.298 1.702 0.298 2.761 0 5-2.239 5-5s-2.239-5-5-5zM12.498 5.002c-0.828 0-1.5-0.672-1.5-1.5s0.672-1.5 1.5-1.5 1.5 0.672 1.5 1.5-0.672 1.5-1.5 1.5z"></path>
        </g>
    </defs>
</svg>

<form class="joms-form" action="<?php echo CRoute::_('index.php?option=' . COM_USER_NAME . '&task=' . COM_USER_TAKS_LOGIN); ?>" method="post" name="login" >
    <div class="joms-input--append">
        <svg viewBox="0 0 16 16" class="joms-icon">
            <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-user"></use>
        </svg>
        <input type="text" name="username" class="joms-input" placeholder="<?php echo JText::_('MOD_HELLOME_USERNAME'); ?>">
    </div>
    <div class="joms-input--append">
        <svg viewBox="0 0 16 16" class="joms-icon">
            <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-lock"></use>
        </svg>
        <input type="password" name="password" class="joms-input" placeholder="<?php echo JText::_('MOD_HELLOME_PASSWORD'); ?>">
    </div>

    <?php if(CSystemHelper::tfaEnabled()){?>
        <div class="joms-input--append">
            <svg viewBox="0 0 16 16" class="joms-icon">
                <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-key"></use>
            </svg>
            <input type="text" name="secretkey" class="joms-input" placeholder="<?php echo JText::_('COM_COMMUNITY_AUTHENTICATION_KEY'); ?>">
        </div>
    <?php } ?>

    <?php if ($usersConfig->get('allowUserRegistration')) : ?>
      <a class="joms-button--secondary joms-button--small" href="<?php echo CRoute::_('index.php?option=com_community&view=register', false); ?>">
        <?php echo JText::_('MOD_HELLOME_REGISTER'); ?>
      </a>
    <?php endif; ?>
    <input type="hidden" name="option" value="<?php echo COM_USER_NAME; ?>"/>
    <input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN; ?>"/>
    <input type="hidden" name="return" value="<?php echo $loginLink; ?>"/>
    <div class="joms-js--token"><?php echo JHTML::_('form.token'); ?></div>


    <?php if ( JPluginHelper::isEnabled('system', 'remember') && $moduleParams->get('remember_me') != 3) { ?>
        <p id="form-login-remember" class="joms-checkbox" style="<?php if($moduleParams->get('remember_me') == 2){ echo 'visibility:hidden'; } ?>">
            <input type="checkbox" value="yes" id="remember" name="remember" <?php if($moduleParams->get('remember_me') == 0 || $moduleParams->get('remember_me') == 2){ echo 'checked'; }?>>
            <span><?php echo JText::_('MOD_HELLOME_REMEMBER_ME'); ?></span>
        </p>
    <?php } ?>

    <button class="btn btn-primary"><?php echo JText::_('MOD_HELLOME_LOGIN'); ?></button>

    <div class="sep"></div>

    <?php if($moduleParams->get('show_facebook')) {
        echo $fbHtml;
    } ?>
    <ul class="top-gap">
			<?php if($moduleParams->get('show_forgotpwd')) { ?>
				<li>
					<a href="<?php echo CRoute::_('index.php?option=' . COM_USER_NAME . '&view=reset'); ?>">
						<span class="fa fa-key" title="<?php echo JText::_('MOD_HELLOME_FORGET_PASSWORD'); ?>" > </span>
					</a>
				</li>
				<?php } ?>

				<?php if($moduleParams->get('show_forgotusr')) { ?>
				<li>
					<a href="<?php echo CRoute::_('index.php?option=' . COM_USER_NAME . '&view=remind'); ?>">
						<span class="fa fa-user" title="<?php echo JText::_('MOD_HELLOME_FORGET_USERNAME'); ?>"></span>
					</a>
				</li>
				<?php } ?>

				<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li>
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=register', false); ?>">
						<span class="fa fa-user-plus" title="<?php echo JText::_('MOD_HELLOME_REGISTER'); ?>" ></span>
					</a>
				</li>
			<?php endif; ?>
    </ul>
</form>

<?php }?>

</div>
