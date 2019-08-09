# SF4 Contact Form Implementation
1. Creates an API endpoint for a contact form using Symfony 4.
2. Assuming a CSV containing data from a legacy website, imports it into a new database.

The data should be validated and persisted in a database:
* E-mail (required, valid email address)
* Message (required, max length 1000)

## Requirements

- `Docker` and `docker-compose` 

## Installation

1. Clone the repository and execute
```sh
cd ./docker && docker-compose up --build
```
2. Add an entry into your `hosts` file:
```sh
127.0.0.1 sf4-contact-form.local
```

## Usage

Visit [http://sf4-contact-form.local](http://sf4-contact-form.local)
