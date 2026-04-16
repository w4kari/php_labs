<?php
declare(strict_types=1);

/**
 * Интерфейс для всех валидаторов приложения.
 */
interface ValidatorInterface
{
    /**
     * Запускает процесс валидации данных.
     *
     * @return bool Возвращает true, если валидация прошла успешно, иначе false.
     */
    public function validate(): bool;

    /**
     * Возвращает массив ошибок валидации.
     *
     * @return array<string, array<int, string>> Ошибки по полям.
     */
    public function errors(): array;

    /**
     * Возвращает очищенные и провалидированные данные.
     *
     * @return array<string, mixed> Валидные данные.
     */
    public function validated(): array;
}