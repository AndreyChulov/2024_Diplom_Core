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
    public static string $GAME_STARTED = "Игра начата";
    public static string $GAME_ALREADY_EXISTS = "Игра уже существует";
    public static string $GAME_STATUS_WAIT_FOR_PLAYER = "Ожидание второго игрока";
    public static string $GAME_STATUS_BLACK_WIN = "Черные выиграли";
    public static string $GAME_STATUS_WHITE_WIN = "Белые выиграли";
    public static string $GAME_STATUS_FINISHED_BY_ADMIN = "Завершена администратором";
    public static string $GAME_INFO_RETRIEVED = "Данные игры получены";
}