.PHONY: clean build

fix:
	php-cs-fixer fix

build:
	composer install

test: reports
	php vendor/bin/phpunit --log-junit reports/ut-junit.xml

reports:
	mkdir -p reports
