<?php

namespace controllers\authorized_users_controllers;
use Exception;
use models\Follows;
class FollowController
{
    private $followModel;
    public function __construct($conn) {
        $this->followModel = new Follows($conn);

    }
    // Метод для подписки на пользователя
    public function follow($follower_id, $followed_id) {

        // Проверка, если уже существует запись о подписке
        $existingFollow = $this->followModel->findByFollowerAndFollowed($follower_id, $followed_id);

        if ($existingFollow) {
            // Если уже есть подписка, возвращаем ошибку или просто ничего не делаем
            echo json_encode(['success' => false, 'message' => 'You are already following this user.']);
            exit();
        }

        // Создание новой подписки
        $follow = new Follows(getDbConnection());
        $follow->follower_id = $follower_id;
        $follow->followed_id = $followed_id;

        if ($follow->save()) {
            echo json_encode(['success' => true, 'message' => 'Successfully followed the user.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error occurred while following the user.']);
        }

        exit();
    }

    // Метод для отписки от пользователя
    public function unfollow($follower_id, $followed_id) {

        // Находим подписку, которую нужно удалить
        $follow = $this->followModel->findByFollowerAndFollowed($follower_id, $followed_id);

        if ($follow) {
            // Удаляем подписку
            if ($follow->delete()) {
                echo json_encode(['success' => true, 'message' => 'Successfully unfollowed the user.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error occurred while unfollowing the user.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'You are not following this user.']);
        }

        exit();
    }
}
