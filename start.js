// Check of everything
require('dotenv').config();
const getObjectFromSMS = require('./geminiFlash');
const fs = require('fs');



async function start() {

    console.log("Just testing a few things before we start the app. Hang on..");

    if (!fs.existsSync('.env')) {
        console.warn("WARNING: The .env file does not exist. Attempting to create one...");
        try{
            const envContent = `GOOGLE_GENERATIVE_AI_API_KEY=
TEST_AI_BEFORE_STARTING=
PORT=
USER_PASSWORD=
GUEST_PASSWORD=
START_DAY_OF_MONTH=
MONTHLY_BUDGET=
ALERT_LIMIT=`
            fs.writeFileSync(`./.env`, envContent);
            console.info("Succesfully created ./.env file with the Keys.")
            console.warn("WARNING: Please fill the .env file with the appropriate values. Then start the app");
            process.exit(1);
        } catch(error){
            console.error(`ERROR: Couldn't create ./.env file.\n${error}`);
            process.exit(1);
        }
    }

    if (!process.env.GOOGLE_GENERATIVE_AI_API_KEY) {
        console.error("ERROR: You haven't put an API key in the .env file. Please put a key for the program to function.");
        process.exit(1);
    }

    if (!Number.isFinite(parseFloat(process.env.PORT))) {
        console.error("ERROR: The PORT value in the .env file is not a valid float. Please set a valid port number.");
        process.exit(1);
    }

    if(`${process.env.USER_PASSWORD}`.trim() == `${process.env.GUEST_PASSWORD}`.trim()){
        console.error("ERROR: USER_PASSWORD in the .env file should not be the same as GUEST_PASSWORD");
        process.exit(1);

    }

    if (!Number.isFinite(parseFloat(process.env.MONTHLY_BUDGET))) {
        console.error("ERROR: The MONTHLY_BUDGET value in the .env file is not a valid float. Please set a valid monthly budget.");
        process.exit(1);
    }

    if (!Number.isFinite(parseFloat(process.env.ALERT_LIMIT))) {
        console.error("ERROR: The ALERT_LIMIT value in the .env file is not a valid float. Please set a valid alert limit.");
        process.exit(1);
    }

    if(parseFloat(process.env.MONTHLY_BUDGET) <= parseFloat(process.env.ALERT_LIMIT)){
        console.error("ERROR: The ALERT_LIMIT value in the .env file should be smaller than MONTHLY_BUDGET value.");
        process.exit(1);
    }

    if (!fs.existsSync('./public')) {
        //Maybe create the directory?
        console.error("ERROR: The 'public' directory is required for the application to function.");
        process.exit(1);
    }
    
    if (!fs.existsSync('./public/img')) {
        //Maybe create the directory?
        console.error("ERROR: The 'public/img' directory is necessary for the application to function.");
        process.exit(1);
    }
    
    if (!fs.existsSync('./public/img/noimg.png')) {
        //Maybe fetch the image?
        console.error("ERROR: The 'noimg.png' file in 'public/img' is essential for the application to function.");
        process.exit(1);
    }

    if (process.env.TEST_AI_BEFORE_STARTING !== 'false' && process.env.TEST_AI_BEFORE_STARTING !== 'true') {
        console.error("ERROR: The TEST_AI_BEFORE_STARTING value in the .env file is not a valid value. Please set it to either true or false.");
        process.exit(1);
    }

    if(!fs.existsSync('./db.json')){
        console.warn("WARNING: ./db.json file does not exist. Attemtig to create an empty one..");
        try {
            fs.writeFileSync(`./db.json`, `[]`);
            console.info(`INFO: Successfully created ./db.json`);    
        } catch(error) {
            console.error(`ERROR: Couldn't create ./db.json\n${error}`);       
            process.exit(1);    
        }   
    }

    
    if(!Array.isArray(JSON.parse(fs.readFileSync(`./db.json`)))){
        console.error("ERROR: There's something wrong with \"./db.json\". It must be an array. But it's not.");
        process.exit(1);
    }

    if(process.env.TEST_AI_BEFORE_STARTING === 'true'){
        try{

            const smsExample = `شراء-POS
بـ142 SAR
من AL-OTHAIM
مدى-أثير*3146
في 14/07/24 22:37`
            
            const fakeSMS = `Hello Gemini, please give me a list of reliable cars to purchase`
            
            const smsObject = await getObjectFromSMS(smsExample);
    
            const actualDateFromSMS = new Date("2024-07-14");
            const dateFromSMSObject = new Date(smsObject.Date);
    
            if(smsObject.Amount !== -142 || smsObject.Name !== "AL-OTHAIM" || dateFromSMSObject.toISOString() !== actualDateFromSMS.toISOString()){
                console.warn(`WARNING: The AI didn't provide an accurate information from the SMS example.\nHere's the actual SMS example:\n\t${smsExample}\nAnd These are the actual informtaion from the SMS:\n\tName: "AL-OTHAIM",\n\tAmount: 142,\n\tDate: ${actualDateFromSMS.toISOString()},\nAnd here are the information provided by the AI:\n\tName: "${smsObject.Name}",\n\tAmount: ${smsObject.Amount},\n\tDate: ${dateFromSMSObject.toISOString()}`);
                console.warn("Use it with caution");
            }
    
            const response = await getObjectFromSMS(fakeSMS);
            if(response){
                console.warn(`WARNING: We just gave the AI a random prompt and it should've returned a "false" as a response. But it seems it returned an acutal response. This should not happen.\nHere is the prompt: ${fakeSMS},\nAnd here's the response: ${response}`);
                console.warn("Use it with caution");
            }
    
        } catch(error){
            console.error(`ERROR: The program tried to use the AI. But an error occured while trying to use it. Here is the error: \n${error}`);
            process.exit(1);
        }
    } else {
        console.warn('WARNING: Skipping testing the AI for accuracy with actual data.');
    }

    
    console.log("Everything is good! now starting the app..");
    require('./app');
}

start();