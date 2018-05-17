/*
  180510
  Proejct Auth.js
  jhkim
*/
// Initialize Firebase
  var config = {
    apiKey: "AIzaSyCa3Nk0MeMkK7VSvo3cCmFQ7ozuWAM4lkk",
    authDomain: "worksystem-bf659.firebaseapp.com",
    databaseURL: "https://worksystem-bf659.firebaseio.com",
    projectId: "worksystem-bf659",
    storageBucket: "worksystem-bf659.appspot.com",
    messagingSenderId: "431407862824"

  };
firebase.initializeApp(config);
var auth, database;
//var Attendances_monthlyRef, Attendances_dailyRef, UsersRef;
database = firebase.database();
auth = firebase.auth();
