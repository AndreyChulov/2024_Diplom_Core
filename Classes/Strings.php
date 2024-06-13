<?php

namespace Classes;

class Strings
{
    public static string $METHOD_GET_NOT_SUPPORTED = "Метод GET не поддерживается";
    public static string $METHOD_POST_NOT_SUPPORTED = "Метод POST не поддерживается";
    public static string $WRONG_REQUEST = "Неправильно сформирован запрос";
    public static string $LOGIN_ALREADY_EXISTS = "Запрошенный Login пользователя уже занят";
    public static string $LOGIN_NOT_EXISTS = "Запрошенный Login пользователя не зарегистрирован";
    public static string $PASSWORD_WRONG = "Пароль неверен";
    public static string $KEY_WRONG = "Ключ не подходит";
    public static string $REGISTRATION_SUCCESSFUL = "Регистрация прошла успешно";
    public static string $AUTHORIZATION_SUCCESSFUL = "Авторизация прошла успешно";
    public static string $KEY_VALID = "Ключ активен";
    public static string $KEY_REMOVED = "Ключ удален";
    public static string $GAME_FORCE_FINISHED = "Игра принудительно завершена";
    public static string $GAME_NEW_STARTED = "Новая игра начата";
    public static string $GAME_PLAYER_CONNECTED = "Игрок подключен к игре";
    public static string $GAME_ALREADY_EXISTS = "Игра уже существует";
    public static string $GAME_NOT_EXISTS = "Игра не существует";
    public static string $GAME_STATUS_WAIT_FOR_PLAYER = "Ожидание второго игрока";
    public static string $GAME_STATUS_WHITE_TURN = "Ход белых";
    public static string $GAME_STATUS_BLACK_TURN = "Ход черных";
    public static string $GAME_STATUS_BLACK_WIN = "Черные выиграли";
    public static string $GAME_STATUS_WHITE_WIN = "Белые выиграли";
    public static string $GAME_STATUS_WHITE_SURRENDER = "Белые сдались";
    public static string $GAME_STATUS_BLACK_SURRENDER = "Черные сдались";
    public static string $GAME_STATUS_FINISHED_BY_ADMIN = "Завершена администратором";
    public static string $GAME_INFO_RETRIEVED = "Данные игры получены";
    public static string $GAME_INFO_UPDATED = "Данные игры обновлены";
    public static string $CHECKER_NOT_FOUND = "Шашка не найдена";
    public static string $MOVE_INVALID = "Ход неверный";
}