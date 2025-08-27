.PHONY: up build clean

up:
	docker-compose up --build

build:
	docker-compose build

clean:
	docker-compose down -v
