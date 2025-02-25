<?php

namespace controllers\authorized_users_controllers;
use Exception;
use models\Follows;
use models\Notifications;
use models\Settings;
use models\User;

class FollowController
{
    private $followModel;
    private $notificationModel;
    private $settingModel;
    private $userModel;
    public function __construct($conn) {
        $this->followModel = new Follows($conn);
        $this->notificationModel = new Notifications(getDbConnection());
        $this->settingModel = new Settings(getDbConnection());
        $this->userModel = new User(getDbConnection());
    }
    // Метод для подписки на пользователя
    public function follow($follower_id, $following_id) {
        header('Content-Type: application/json');
        if($follower_id === null){
            echo json_encode(['success' => false, 'message' => 'You must be logged in before you can follow a user.']);
            exit();
        }
        $followModel = $this->followModel;
        $profileVisibility = $this->settingModel->getProfileVisibility($following_id);

        if ($profileVisibility === 'private') {
            if ($followModel->createFollowRequest($follower_id, $following_id)) {
                $followersCount = $followModel->getFollowersCount($following_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Follow request sent.',
                    'nextAction' => 'cancel',
                    'followersCount' => $followersCount,
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error occurred while sending follow request.']);
            }
        } else {
            if ($followModel->save($follower_id, $following_id)) {
                $followersCount = $followModel->getFollowersCount($following_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully followed the user.',
                    'nextAction' => 'unfollow',
                    'followersCount' => $followersCount,
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error occurred while following the user.']);
            }
        }
        exit();
    }


    // Метод для отписки от пользователя
    public function unfollow($follower_id, $following_id) {
        // Используем уже инициализированную модель $this->followModel
        $follow = $this->followModel->findByFollowerAndFollowed($follower_id, $following_id);

        if ($follow) {
            // Удаляем подписку
            if ($follow->delete($follower_id, $following_id)) {
                echo json_encode(['success' => true, 'message' => 'Successfully unfollowed the user.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error occurred while unfollowing the user.']);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'You are not following this user.']);
        }

        exit();
    }

    public function showFollowers($userId)
    {
        session_start();
        require_once 'app/services/helpers/switch_language.php';

        // Получаем подписчиков через модель
        $followers = $this->followModel->getFollowers($userId);
        $followersCount = $this->followModel->getFollowersCount($userId);
        $user = $_SESSION['user'] ?? null;
        $followingId = $userId;
//        var_dump($userId);
//        var_dump($followers);
        // Подключаем представление
        include 'app/views/common_templates/followers_template.php';
    }

    public function showFollowings($userId)
    {
        session_start();
        require_once 'app/services/helpers/switch_language.php';

        // Получаем подписки через модель
        $followings = $this->followModel->getFollowings($userId);
        $followingsCount = $this->followModel->getFollowingCount($userId);

        // Подключаем представление
        include 'app/views/common_templates/followings_template.php';
    }
    public function cancelFollowRequest($followerId, $followingId) {

        if(!$followerId && !$followingId){
            echo json_encode(['success' => false, 'message' => 'Failed to cancel follow request']);
        exit();
        }
        // Выполняем отмену запроса
        $this->followModel->cancelRequest($followerId, $followingId);
        echo json_encode(['success' => true, 'message' => 'Follow request cancelled successfully']);

        // Завершаем выполнение, чтобы не отправлять лишние данные
        exit();
    }

}
