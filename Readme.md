# Symfony REST приложение для расчета цены продукта и проведения оплаты

## Запуск
1. git clone https://github.com/tim31al/test-systeme.git app_dir
2. cd app_dir
3. make build && make up
4. make init
5. make tests

## Функционал
1. Расчет цены
    ```bash
    curl --location 'http://localhost:80/calculate-price' \
        --header 'Content-Type: application/json' \
        --data '{
            "product": 1,
            "taxNumber": "DE123456789",
            "couponCode": "D95"
        }'
    ```
2. Покупка
    ```bash
    curl --location 'http://localhost:80/purchase' \
        --header 'Content-Type: application/json' \
        --data '{
            "product": 1,
            "taxNumber": "DE123456789",
            "couponCode": "D15",
            "paymentProcessor": "paypal"
        }'
    ```
   