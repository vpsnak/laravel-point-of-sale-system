# plantshed

## Project setup

```
npm install
```

### Compiles and hot-reloads for development

```
npm run serve
```

### Compiles and minifies for production

```
npm run build
```

### Run your tests

```
npm run test
```

### Lints and fixes files

```
npm run lint
```

See [Configuration Reference](https://cli.vuejs.org/config/).

### Magento

In order to send / get data from to / from magento you need to authenticate by accessing `/api/magento/authorize` from a browser and login.

| Command           | Description                   |
| ----------------- | ----------------------------- |
| w2:get:customers  | Get customers from magento    |
| w2:send:customers | Send new customers to magento |
| w2:get:products   | Get products from magento     |

\***\*Magento product stock is getting synced to magento on order create / update.**

### MAS

POS orders are posted in MAS network from MasOrderController when a new order is placed ProcessOrder JOB handles stock sync and from there MasOrderController is getting called.
Usage `MasOrderController::submitToMas($this->order);`

- static submitToMas -> parse order data to MAS format
- static parseOrderItems -> parse order items to MAS format
- static parseOrderPayments -> parse order payments to MAS format
- static parseOrderRecipient -> parse order recipient to MAS format `@TODO if customer is guest data to send`
- static log -> parse log messages with prefix to make them consistent and easier to find

\***\*MAS Submitted orders are stored in db from MasOrder model with the id MAS returns on successful submission.**

Example Request

```
{
  "SenderMdNumber": "USZZ000035",
  "FulfillerMDNumber": "USZZ000035",
  "PriorityType": "1",
  "MessageType": "0",
  "MessageText": "Here is some text for this message.",
  "OrderItems": [
    {
      "ItemCode": "webo2-test",
      "ItemName": "One Dozen Roses Arranged",
      "ItemDescription": "One Dozen Roses with Filler and Greens",
      "ItemCost": "00.00",
      "Quantity": "1"
    }
  ],
       "RecipientDetail": {
        "ExtensionData": null,
        "RecipientFirstName": "Test",
        "RecipientLastName": "Order",
        "RecipientAttention": "Test Company",
        "RecipientEmail": "test@testing.com",
        "RecipientAddress": "123 Test Dr",
        "RecipientAddress2": "Suite 23",
        "RecipientCity": "Dallas",
        "RecipientState": "TX",
        "RecipientCountry": "US",
        "RecipientZip": "80211",
        "RecipientPhone": "2149994455",
        "RecipientLatLong": "",
        "SpecialInstructions": "",
        "DeliveryDate": "02\/14\/2030",
        "DeliveryEndDate": "02\/14\/2030",
        "CardMessage": "This is a message for you",
        "OccasionType": 2,
        "ResidenceType": 6
       }
}
```

What we send

```
{
  'SenderMdNumber': 'USZZ000035',
  'FulfillerMDNumber': 'USZZ000035',
  'PriorityType': 1,
  'MessageType': 0,
  'MessageText': 'test comment',
  'RecipientDetail': {
    'ExtensionData': null,
    'RecipientFirstName': 'Test',
    'RecipientLastName': 'Order',
    'RecipientAttention': 'Test Company',
    'RecipientEmail': 'test@testing.com',
    'RecipientAddress': '123 Test Dr',
    'RecipientAddress2': 'Suite 23',
    'RecipientCity': 'Dallas',
    'RecipientState': 'TX',
    'RecipientCountry': 'US',
    'RecipientZip': '80211',
    'RecipientPhone': '2149994455',
    'RecipientLatLong': '',
    'SpecialInstructions': '',
    'DeliveryDate': '02\/14\/2030',
    'DeliveryEndDate': '02\/14\/2030',
    'CardMessage': 'This is a message for you!',
    'OccasionType': 2,
    'ResidenceType': 6
  },
  'OrderItems': [{
    'ItemCode': 'cb486c85-0ff5-3aa7-8352-09951173b28d',
    'ItemName': 'Blooming Desert Duo',
    'ItemDescription': '-',
    'ItemCost': '3.00',
    'Quantity': 1,
    'DiscountAmount': '1.00'
  }, {
    'ItemCode': '35d2bb82-7e18-3a51-8bef-0713b658b7cb',
    'ItemName': 'Blooming Desert Duo',
    'ItemDescription': '-',
    'ItemCost': '60.00',
    'Quantity': 1,
    'DiscountAmount': '54.00'
  }],
  'Payments': [{'PaymentAmount': '20.00', 'PaymentType': 2, 'GiftCardNumber': 'dev'}, {
    'PaymentAmount': '40.50',
    'PaymentType': 7
  }],
  'MdseAmount': 55,
  'TaxAmount': 5.5,
  'TotalAmount': 60.5,
  'OrderId': 4
}
```

### MAS Chris Golden Guidelines

Below are the guidlines we have from MAS ATM. (more can be found on paymo attached email)

Priority type will always be a static “1”, this is reserved for florist to florist orders

Delivery Time slots will be sent in SpecialInstructions , you can also put an abbreviated version in ShippingPriority (IE> 5P-9P for 5pm to 9pm)

Please send your order number in MessageText, we place this on our reference field in the MAS order (Ilias – it goes on line 9)

OrderId and FulFiller Order ID, are also reserved, you do not have to populate those. OrderId is the generated order number on successful post to API, and FulfillerId comes back on an acknowledgment message to you

ExtensionData is part of the object for JSON

Any residence or occasion type that falls out of what the API has, would be considered “Other”, any residence type outside of the API would default to Home or Business, depending on the closest match.

We can send screen shots of how the order populates. We have a legacy system and access is not optimal for outside access.

Live environment is only a URL change. You can post to our Azure Functions server with https://api.masdirectnetwork.com/api/messages

Same username, masdirectid, and password
