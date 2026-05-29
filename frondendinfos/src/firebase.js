import { initializeApp } from "firebase/app";
import { getDatabase } from "firebase/database";

const firebaseConfig = {
  apiKey: "AIzaSyBdliVOPS29YdjDNh0p6CXplfJWGlAmhmM",
  authDomain: "infos-smk.firebaseapp.com",
  databaseURL: "https://infos-smk-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "infos-smk",
  storageBucket: "infos-smk.firebasestorage.app",
  messagingSenderId: "649302551668",
  appId: "1:649302551668:web:9e0e0c9321b39422eb3521"
};

const app = initializeApp(firebaseConfig);
const database = getDatabase(app);

export { database };
