const express = require('express');
const bodyParser = require('body-parser');
const session = require('express-session');
const app = express();
require('dotenv').config();
const port = process.env.PORT;
const getObjectFromSMS = require('./geminiFlash');
const fs = require('fs');
const path = require('path');



//Multer
const multer = require('multer') 
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, `./public/img`)
    },
    filename: (req, file, cb) => {
        cb(null, `${Date.now()}${path.extname(file.originalname)}`)
    },
})
const upload = multer({
    storage: storage,
});
const sharp = require('sharp');



// Middleware to parse JSON
app.use(express.json());

// Middleware to parse incoming request bodies
app.use(bodyParser.urlencoded({ extended: true }));

//Register session middleware
app.use(session({
    secret: 'meow',
    cookie: {maxAge: 86400000},
    saveUninitialized: true
}))

function guestAuth(req, res, next){
    if(req.session.guestAuthenticated){
        next()
    } else {
        res.redirect('/login')
    }
}



app.get('/guestViewData', guestAuth, async (req, res) => {
    const transactions = await readTransactions()
    res.render(`guest`, {
        transactions: transactions,
        viewMode: "كل العمليّات",
    })
});

app.get('/guestViewDataCurrentMonth', guestAuth, async (req, res) => {
    const thisMonthTransactions = await getTransactionsThisMonth();
    res.render(`guest`, {
        transactions: thisMonthTransactions,
        viewMode: "عمليّات هذا الشهر",
    })
});

app.get('/guestViewDataSpecificRange', guestAuth, async (req, res) => {
    const startDate = req.query.startDate;
    const endDate = req.query.endDate;


    if (isNaN(Date.parse(startDate)) || isNaN(Date.parse(endDate))){
        res.render(`requestStatus`, {
            err: {
                message: `
الي دخلته ماهو تاريخ اصلًا! 
حاول مرة ثانية
                `,
            }
        })
        return;
    }
    const startDateObj = new Date(startDate);
    const endDateObj = new Date(endDate);
    if (startDateObj >= endDateObj) {
        res.render(`requestStatus`, {
            err: {
                message: `
تاريخ البداية ما يصلح يجي بعد النهاية. 
حاول مرة ثانية
`,
            }
        })
        return;

    }

    const transactions = await getTransactionsInCustomRange(startDate, endDate);
    res.render(`guest`, {
        transactions: transactions,
        viewMode: `علميّات من تاريخ ${startDate} الى تاريخ ${endDate}`,
    })
});



function auth(req, res, next){
    if(req.session.authenticated){
        next()
    } else {
        res.redirect(`login`)
    }
}

app.get('/login', async (req, res) => {
    if(req.session.guestAuthenticated){
        res.redirect('/guestViewData');
        return;
    }

    if(req.session.authenticated){
        res.redirect('/');
        return;
    }

    res.render(`login`)
});

//brute-force attack protection LOL
let failedAttempts = 0;
app.post('/login', async (req, res) => {

    if(failedAttempts >= 6){
        res.render(`login`, {
            err: `
تم تعطيل تسجيل الدخول بسبب محاولات كثيرة فاشلة.
سكر وأفتح التطبيق مرة ثانية عشان تقدر تسجل دخول
            `
        })   
        return;
    }

    const password = `${req.body.password}`.trim()
    if(password == process.env.USER_PASSWORD){
        req.session.authenticated = true;
        failedAttempts = 0;
        res.redirect('/')
        return;
    }
    if(password == process.env.GUEST_PASSWORD){
        req.session.guestAuthenticated = true;
        failedAttempts = 0;
        res.redirect('/guestViewData')
        return;
    }
    failedAttempts++
    res.render(`login`, {
        err: "معليش, الباسورد خطأ"
    })   
    return; 

});

app.get('/logout', async (req, res) => {
    if(req.session.authenticated){
        req.session.authenticated = false;
    }
    if(req.session.guestAuthenticated){
        req.session.guestAuthenticated = false;
    }
    res.redirect('/login');
});

app.use(express.static(`public`));
app.use(auth);
app.set('view engine', 'ejs');

// Main route
app.get('/', auth, async (req, res) => {
    res.render(`mainForm`, {
        insight: await getInsight(),
    })
});

app.get('/viewData', async (req, res) => {

    //Get all transactions
    const transactions = await readTransactions()
    res.render(`spendingList`, {
        transactions: transactions,
        viewMode: "كل العمليّات",
    })
});

app.get('/viewDataCurrentMonth', async (req, res) => {

    //Get all transactions
    const thisMonthTransactions = await getTransactionsThisMonth();
    res.render(`spendingList`, {
        transactions: thisMonthTransactions,
        viewMode: "عمليّات هذا الشهر",
    })
});

app.get('/viewDataSpecificRange', async (req, res) => {
    const startDate = req.query.startDate;
    const endDate = req.query.endDate;


    if (isNaN(Date.parse(startDate)) || isNaN(Date.parse(endDate))){
        res.render(`requestStatus`, {
            err: {
                message: `
الي دخلته ماهو تاريخ اصلًا! 
حاول مرة ثانية
                `,
            }
        })
        return;
    }
    const startDateObj = new Date(startDate);
    const endDateObj = new Date(endDate);
    if (startDateObj >= endDateObj) {
        res.render(`requestStatus`, {
            err: {
                message: `
تاريخ البداية ما يصلح يجي بعد النهاية. 
حاول مرة ثانية
`,
            }
        })
        return;

    }

    const transactions = await getTransactionsInCustomRange(startDate, endDate);
    res.render(`spendingList`, {
        transactions: transactions,
        viewMode: `علميّات من تاريخ ${startDate} الى تاريخ ${endDate}`,
    })
});


app.post('/postSMS', upload.single(`image`) ,async (req, res) => {
    
    //Take the inputs from the user
    const smsContent = `${req.body.smsContent}`.trim() // Must have
    const noteContent = `${req.body.note}`.trim() || "لا يوجد"; // Optional
    let ImagePath = `./img/noimg.png`; // Optional
    if(req.file){
        await sharp(req.file.path).jpeg({quality: 50}).toFile(`./public/img/compressed_${req.file.filename}`)
        ImagePath =  `./img/compressed_${req.file.filename}`;
    }
    
    // Convert the SMS to Ojbect
    const transaction = await getObjectFromSMS(smsContent);
    if (!transaction) {

        res.render(`requestStatus`, {
            err: {
                message: `
معليش, الي حطيته ماهي رسالة عملية شراء
لا تجرب مرة ثانية لان عدد الريكويست للـ أي بي آي محدود
`,
            }
        })
        return
    }

    transaction.Note = noteContent;
    transaction.ActaulMessage = smsContent
    transaction.ImagePath = ImagePath;
    //Maybe add current date?

    if(await isTransactionExsists(transaction)){
        const duplicateTransactionIndex = await findTransactionIndex(transaction);
        res.render(`requestStatus`, {
            err: {
                message: `
${duplicateTransactionIndex + 1} :العمليّة اللي قاعد تحاول تدخّلها موجودة مسبقًا برقم
                `,
                transaction: transaction, 
            },
        })
        return;
    }

    
    const tranactionStatus = await storeTransaction(transaction);
    if (!tranactionStatus) {
        res.render(`requestStatus`, {
            err: {
                message: `
معليش, حصل خطأ لما حاولت اخزن العملية. 
`,
            }
        })
        return;
    }



    res.render(`requestStatus`, {
        succ: {
            message: `
تم! خزنت العملية في قاعدة البيانات`,
    transaction: transaction,
        },
    })
    return;
});

app.get('/deleteTransaction/:ActaulMessage', async (req, res) => {
    const ActaulMessage = decodeURIComponent(req.params.ActaulMessage);

    const status = await deleteTransaction(ActaulMessage);
    if(!status){
        res.render(`requestStatus`, {
            err: {
                message: `
معليش, صار خطأ لما حاولت احذف العملية
`,
                ActaulMessage: ActaulMessage, //The sms message
            },
        })
        return;
    }
    if(status == 404){
        res.render(`requestStatus`, {
            err: {
                message: `
معليش, العملية الي قاعد تحاول تحذفها مو موجودة
                `,
                ActaulMessage: ActaulMessage, //The sms message
            },
        })
        return;
    }

    res.render(`requestStatus`, {
        succ: {
            message: `
حذفت العمليّة بناءًا على طلبك
`,
            ActaulMessage: ActaulMessage, //The sms message
        },
    })

});

app.get('/settings', auth, async (req, res) => {
    res.render(`settings`)
});


// Start the server
app.listen(port, () => {
    console.log(`App is running at http://localhost:${port}`);
});

/**
 * Return insight of all transactions
 * @returns {{ totalAmount: number, spendingThisMonth: number }}
 */
async function getInsight() {
    // Get transactions data
    let transactions = await readTransactions();

    // Calculate the total amount of transactions
    let totalAmount = 0;
    for (let i = 0; i < transactions.length; i++){
        totalAmount += transactions[i].Amount;
    }

    // Calculate total spending of all time.
    let spendingTotal = 0;
    for (let i = 0; i < transactions.length; i++) {
        if (transactions[i].Amount < 0) {
            spendingTotal += transactions[i].Amount;
        }
        
    }

    // Filter transactions for the current month
    let transactionsThisMonth = await getTransactionsThisMonth();
    

    // Calculate spending for the current month
    let spendingThisMonth = 0;
    for (let i = 0; i < transactionsThisMonth.length; i++) {
        if (transactionsThisMonth[i].Amount < 0) {
            spendingThisMonth += transactionsThisMonth[i].Amount;
        }
    }

    totalAmount = Math.round(totalAmount)
    spendingThisMonth = Math.round(spendingThisMonth)
    spendingTotal = Math.round(spendingTotal)
    return {
        totalAmount, //current total amount.
        spendingThisMonth, //spending this month.
        spendingTotal, //not useful
    }
    
}

/**
 * 
 * @param { { Name: string, Amount: string, Date: string, Note: string } } transaction The transaction object
 * @returns { boolean } `true` if `transaction` exists in the DB. `false` if it doesn't
 */
async function isTransactionExsists(transaction){
    const allTransaction = await readTransactions();

    for(let i = 0; i < allTransaction.length; i++){
        if(allTransaction[i].ActaulMessage == transaction.ActaulMessage){
            return true;
        }
    }
    return false;
}

/**
 * 
 * @param {{ Name: string, Amount: string, Date: string, Note: string }} transaction
 * @returns { number | false } The index number for `transaction`. Or `false` if non.
 */
async function findTransactionIndex(transaction){

    const allTransaction = await readTransactions();
    for(let i = 0; i < allTransaction.length; i++){
        if(allTransaction[i].ActaulMessage == transaction.ActaulMessage){
            return i;
        }
    }
    return false;

}

/**
 * Return an array of transactions for current month
 * @returns {[{ Name: string, Amount: string, Date: string, Note: string }]}
 */
async function getTransactionsThisMonth() {
    const startDayOfMonth = process.env.START_DAY_OF_MONTH;

    const currentDate = new Date();
    let currentYear = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();

    // Adjust the current month and year if the current date is before the startDayOfMonth
    if (currentDate.getDate() < startDayOfMonth) {
        if (currentMonth === 0) {
            currentMonth = 11; // Set to December of the previous year
            currentYear -= 1;
        } else {
            currentMonth -= 1; // Move to the previous month
        }
    }

    // Set the start date to the startDayOfMonth of the current month (or previous month if adjusted)
    const startDate = new Date(currentYear, currentMonth, startDayOfMonth);

    // Calculate the end date for the day before the startDayOfMonth of the next month
    let nextMonth = currentMonth + 1;
    let nextYear = currentYear;
    if (nextMonth > 11) { // December to January transition
        nextMonth = 0; // January is 0
        nextYear += 1;
    }

    // Set the end date to the day before the startDayOfMonth of the next month
    const endDate = new Date(nextYear, nextMonth, startDayOfMonth - 1);
    endDate.setHours(23, 59, 59, 999); // Include the entire end day

    // Get all transactions
    let transactions = await readTransactions();

    // Filter transactions
    let transactionsThisMonth = [];
    for (let i = 0; i < transactions.length; i++) {
        const transactionDate = new Date(transactions[i].Date);
        if (transactionDate >= startDate && transactionDate <= endDate) {
            transactionsThisMonth.push(transactions[i]);
        }
    }

    return transactionsThisMonth;
}

async function getTransactionsInCustomRange(startDateStr, endDateStr) {
    // Convert the startDateStr and endDateStr into Date objects
    const startDate = new Date(startDateStr);
    const endDate = new Date(endDateStr);

    // Ensure endDate includes the entire end day
    endDate.setHours(23, 59, 59, 999);

    // Validate the date conversion
    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
        throw new Error("Invalid date format. Please use 'YYYY-MM-DD' format.");
    }

    // Get all transactions
    let transactions = await readTransactions();

    // Filter transactions for the custom date range
    let transactionsInRange = transactions.filter(transaction => {
        const transactionDate = new Date(transaction.Date);
        return transactionDate >= startDate && transactionDate <= endDate;
    });

    return transactionsInRange;
}

/**
 * Store a single transaction extracted from an SMS.
 * @param {{ Name: string, Amount: string, Date: string, Note: string }} transaction The transaction object to store 
 * @returns { boolean } `boolean` For the storing status: `true` means the storing was successful, `false` means an error occured.
 */
async function storeTransaction(transaction) {
    let transactions = await readTransactions();
    if (!transactions) return false;


    transactions.push(transaction)

    return await writeTransactions(transactions)
}

/**
 * Delete a `transaction` using the SMS message (`transaction.ActaulMessage`)
 * @param { string } ActaulMessage 
 * @returns { boolean | 404 } `true` if successfuly deleted a transaction, `false` otherwise. `404` if no transaction with `ActaulMessage`
 */
async function deleteTransaction(ActaulMessage){
    try{
        let transactions = await readTransactions();

        for(let i = 0; i < transactions.length; i++){
            if(transactions[i].ActaulMessage == ActaulMessage){
                transactions.splice(i, 1);
                return await writeTransactions(transactions);
            }
        }
        return 404;
    } catch(err){
        return false;
    }
}

/**
 * return the transactions as array.
 * @returns {[{ Name: string, Amount: string, Date: string, Note: string }] | false}
 */
function readTransactions() {
    try {
        const DBtext = fs.readFileSync(`./db.json`);
        DB = JSON.parse(DBtext);
        if(!Array.isArray(DB)) return false;
        return DB;
    } catch (err) {
        return false;
    }
}

/**
 * 
 * @param {[{ Name: string, Amount: string, Date: string, Note: string }]} transactions 
 * @returns {boolean}
 */
async function writeTransactions(transactions) {
    try {
        fs.writeFileSync('./db.json', JSON.stringify(transactions, null, 4));
        //return true;
        return transactions.length;
    } catch (err) {
        return false;
    }
}



