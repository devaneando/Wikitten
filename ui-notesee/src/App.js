import React, { useState, useEffect } from "react";
import "./App.css";
import MdEditor from "./components/MdEditor";

function App() {
  const [markdown, setMarkdown] = useState(``);
  const [loading, setLoading] = useState(true);
  const [path, setPath] = useState("");

  // Similar to componentDidMount and componentDidUpdate:
  useEffect(() => {
    // Update the document title using the browser API
    load();
  });

  const load = async function () {
    let pathname = window.location.pathname;
    console.log(pathname);
    const prefix = "https://www.lilplaytime.com/notesee-api";
    if(pathname === '/')
    {
      pathname = '/inbox.md';
    }
    // const url = `${Constants.REST_ENDPOINT}record/`;
    try {
      const response = await fetch(prefix + pathname, {
        method: "GET", // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        credentials: "same-origin", // include, *same-origin, omit
        headers: {
          "Content-Type": "application/json",
        },
        redirect: "follow", // manual, *follow, error
        referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
      });

      if (response.ok) {
        const results = await response.json();

        console.log(results);
        setMarkdown(results.source);
        setPath(pathname.substring(1));

        setLoading(false);
      } else {
        console.log("Network response was not ok.");
      }
    } catch (error) {
      alert("Error: " + error);
    }
  };

  return (
    <div className="App">
      <button onClick={(e) => load()}>href</button>
      <div>Show File Tree</div>
      {loading ? (
        <span>Loading</span>
      ) : (
        <MdEditor content={markdown} path={path} />
      )}
    </div>
  );
}

export default App;
