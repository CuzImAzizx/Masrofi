# Quick Guide for Using Masrofi

## Login Page

When you launch the app and visit 'http://localhost:[PORT]', the login prompt will appear. You can log in using either the `USER_PASSWORD` or `GUEST_PASSWORD` specified in the `.env` file.

![alt text](/img/image.png)

## Main Page

Upon logging in as a user, the main page will be displayed. Here, you can input your transactions and get a snapshot of your account.

![alt text](/img/image-4.png)

To input a transaction, start by entering the transaction message in the first field. You can also attach an image and add notes to the transaction.

![alt text](/img/image-5.png)

Once you submit the transaction, the AI will process the message, extract the relevant information, and store the transaction object in `./db.json`. The app will then notify you of the success or failure of the process.

![alt text](/img/image-6.png)

That's all there is to it! Input your transactions, and they will be securely stored. You can easily access your transaction history.

## Transaction History

To view your transaction history, visit `http://localhost:[PORT]/viewData`, or click the "أطلع أكثر على كل البيانات" button on the main page.

![alt text](/img/image-7.png)

You can see all transactions, transactions from the current month, or transactions within a specific date range. Furthermore, you can inspect a specific transaction for details or delete transactions directly from this page.

## Guest Page

Guest users can access the app by entering the `GUEST_PASSWORD` on the login page. While guests can view all transactions and apply filters, they are restricted from inserting or deleting transactions.

![alt text](/img/image-9.png)