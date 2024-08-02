# Masrofi مصروفي
Masrofi is a simple express.js single-user app for tracking personal expenses.

![alt text](/img/image-1.png)

## What is special about Masrofi?
It uses Google generative AI to analyze SMS transaction messages and extract useful information as JSON objects. Then, using these JSON objects, you can filter transactions by date and generate summaries of expenses for any specified time period.

![alt text](/img/image-2.png)


## Problem statement
I started this as a personal project to solve a problem I had. That is I get a transfer with amount X and it's only for groceries. So every time I buy groceries with my account, I take the SMS transaction message and put it in the app. To differentiate the grocery expenses from my personal expenses. It would've been easier to open a second bank account for free and track my grocery expenses there, but that wouldn't be fun would it?


## How to install
To install and start using Masrofi to track your expenses, you can clone the project.
### Clone the project
To clone the project, you will need:


1. [**Node v20 or above**](https://nodejs.org/en/download/package-manager)
2. [**Git**](https://git-scm.com/downloads)
3. [**Google Generitave AI API Key**](https://ai.google.dev/gemini-api/docs/api-key#:~:text=You%20can%20create%20a%20key%20with%20one%20click%20in%20Google%20AI%20Studio.)


First clone the project using git
```
git clone https://github.com/CuzImAzizx/Masrofi.git
```
Then, navigate into the cloned project
```
cd ./Masrofi
```
Then, install the dependencies using npm
```
npm install
```
Finally, start the app
```
npm start
```
The app will generate an ./env file where you can configure various aspects of the application.
Here's a sample of the environment file:
```
GOOGLE_GENERATIVE_AI_API_KEY=
TEST_AI_BEFORE_STARTING=
PORT=
USER_PASSWORD=
GUEST_PASSWORD=
START_DAY_OF_MONTH=
MONTHLY_BUDGET=
ALERT_LIMIT=
```
`GOOGLE_GENERATIVE_AI_API_KEY` Here you put your API key for your Google generative AI. You can get one for free [here](https://ai.google.dev/gemini-api/docs/api-key#:~:text=You%20can%20create%20a%20key%20with%20one%20click%20in%20Google%20AI%20Studio)


`TEST_AI_BEFORE_STARTING` You set this as either `ture` or `false`. It tests the AI with real data before starting the app. It's recommended to set it `true`.


`PORT` The port for the app to run. Set it `4000` or any available port.


`USER_PASSWORD` Here you set the "password" for the main user, that is who is able to insert transactions. This is usually you. So set a password for you. And please, don't put your actual password.


`GUEST_PASSWORD` This is the "password" for people who you want to share your transactions with. They are only able to view the transaction history and filter them. They can not insert or delete any transaction. So set a password for them.


`START_DAY_OF_MONTH`: This setting allows you to define the beginning of your monthly cycle. If you consider the first day of the month as the starting point, set this value to `1`.


`MONTHLY_BUDGET` Here you specify the budget for your monthly spending. For example `500`. You will get alerts if you get close or pass the budget.


`ALERT_LIMIT` Here you specify the limit on which when you pass it you will get alerts. For example, if you set it to `200`, and if what is left of your `MONTHLY_BUDGET` is `200`,  you will get alerts.

After you set those variables, now run the app again
```
npm start
```
The app should show something like this
```
...
App is running at http://localhost:[YOUR_PORT]
```
Now go to your browser and enter `http://localhost:[YOUR_PORT]` and you can use the app.

## How to use Masrofi
Masrofi is a user-friendly web application. For detailed instructions, refer to the [full guide here](GUIDE.md).


