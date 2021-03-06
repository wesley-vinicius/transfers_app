include .env

install: 
	@docker-compose up -d
	@docker exec -it ${NAME_CONTAINER}_php composer update
	@docker exec -it ${NAME_CONTAINER}_php php artisan key:generate
	@docker exec -it ${NAME_CONTAINER}_php php artisan migrate
	@docker exec -it ${NAME_CONTAINER}_php php artisan db:seed
	@docker exec -it ${NAME_CONTAINER}_php chmod -R 777 storage/
	@docker exec -it ${NAME_CONTAINER}_php php artisan queue:work

docker-up:
	@docker-compose up -d

docker-build:
	@docker-compose up -d --build

docker-stop:
	@docker-compose stop

docker-down:
	@docker-compose down

docker-status:
	@docker-compose ps

docker-logs:
	@docker-compose logs -f

docker-exec-php:
	@docker exec -it ${NAME_CONTAINER}_php bash

work-start:
	@docker exec -it ${NAME_CONTAINER}_php php artisan queue:work

test:
	@docker exec -it ${NAME_CONTAINER}_php php artisan test --coverage-html tests/coverage/html

test-unit:
	@docker exec -it ${NAME_CONTAINER}_php php artisan test --testsuite=Unit

php-insights:
	@docker exec -it ${NAME_CONTAINER}_php php artisan insights app/Domain

migrate:
	@docker exec -it ${NAME_CONTAINER}_php php artisan migrate

migrate-rollback:
	@docker exec -it ${NAME_CONTAINER}_php php artisan migrate:rollback

migrate-refresh:
	@docker exec -it ${NAME_CONTAINER}_php php artisan migrate:refresh
