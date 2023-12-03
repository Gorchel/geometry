1) Устанавливаем проект (прописываем .env)
2) Запускаем докер согласно ларавель документации Sail
3) docker exec -i geometry_project bash -c "cd /var/www/html && php artisan migrate" - проводим миграции
4) Пример обновления таблицы properties 

docker exec -i geometry_project bash -c "cd /var/www/html && php artisan update_tables:init property true"

Сайт доступен по адресу http://localhost/
