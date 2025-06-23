<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class FirebaseService
{
    protected Auth $auth;
    protected Database $database;

   public function __construct()
{
    $factory = (new Factory)
        ->withServiceAccount(config('services.firebase.credentials_file'))
        ->withDatabaseUri('https://vvp-swami-a787a-default-rtdb.firebaseio.com'); // ✅ Correct DB URL

    $this->auth = $factory->createAuth();
    $this->database = $factory->createDatabase();
}


    // 🔹 Create user in Firebase Authentication
    public function createFirebaseUser(array $data): string
    {
        $user = $this->auth->createUser([
            'email' => $data['email'],
            'password' => $data['password'],
            'displayName' => $data['name'],
        ]);

        return $user->uid;
    }

    // 🔹 Update Firebase Auth user
    public function updateFirebaseUser(string $uid, array $data): void
    {
        $update = [];

        if (isset($data['email'])) {
            $update['email'] = $data['email'];
        }

        if (isset($data['name'])) {
            $update['displayName'] = $data['name'];
        }

        $this->auth->updateUser($uid, $update);
    }

    // 🔹 Delete Firebase Auth user
    public function deleteFirebaseUser(string $uid): void
    {
        $this->auth->deleteUser($uid);
    }

    // 🔹 Sync user to Firebase Realtime Database
    public function syncUser($id, array $data): void
    {
        $this->database->getReference('users/' . $id)->set($data);
    }

    // 🔹 Remove user from Realtime Database
    public function deleteUser($id): void
    {
        $this->database->getReference('users/' . $id)->remove();
    }

    // 🔹 Get Firebase Auth users
    public function getAuthUsers(): array
    {
        $users = [];
        foreach ($this->auth->listUsers() as $user) {
            $users[] = [
                'uid' => $user->uid,
                'email' => $user->email,
                'name' => $user->displayName,
            ];
        }
        return $users;
    }

    // 🔹 Get users from Realtime Database
    public function getRealtimeUsers(): array
    {
        return $this->database->getReference('users')->getValue() ?? [];
    }
}
