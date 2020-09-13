import React, { useState, Fragment } from "react";
import Editor from "wrap-md-editor";
import langSetting from "../lang";
const MdEditor = (props) => {
  const [markdown, setMarkdown] = useState(props.content);

  const save = async function () {
    console.log(markdown);
    console.log(props.path);
    const ref = btoa(props.path);

    console.log(ref);
    //make fetch call
    // const formData = {
    //     ref,
    //     source: markdown
    // }

    const formData = new URLSearchParams();

    formData.append("ref", ref);
    formData.append("source", markdown);
    const prefix = "https://www.lilplaytime.com/notesee-api";
    try {
      const response = await fetch(prefix + "/?a=edit", {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        //   credentials: "same-origin", // include, *same-origin, omit
        //   headers: {
        //     "Content-Type": "application/json",
        //   },
        //   redirect: "follow", // manual, *follow, error
        //   referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: formData,
      });

      if (response.ok) {
        //   const results = await response.json();
        alert("saved");
        console.log(response);
      } else {
        console.log("Network response was not ok.");
      }
    } catch (error) {
      alert("Error: " + error);
    }
  };

  console.log(props.content);
  return (
    <Fragment>
      <Editor
        config={{
          // testEditor.getMarkdown().replace(/`/g, '\\`')
          markdown: props.content,
          onchange: (editor) => {
            console.log("onchange2 =>", editor.getMarkdown());

            const modified = editor.getMarkdown();

            let newCursor = editor.getCursor();
            console.log(newCursor);
            const regex = /\[\[(.+?)\]\][ ]/g;
            let match = regex.exec(modified);
            console.log(match);
            const found = modified.match(regex);
            if (found) {
              console.log(match["1"]);

              const title = match["1"].replace(/-/g, " ");
              const filename = match["1"].replace(/ /g, "-");

              console.log(match.index + " " + regex.lastIndex);

              const matchLen = match["1"].length;
              const replaced = modified.replace(
                regex,
                `[${title}](${filename}.md)`
              );

              // set the text
              editor.clear();
              editor.insertValue(replaced);

              //place the cursor
              console.log(newCursor.ch, matchLen);
              newCursor.ch = newCursor.ch + matchLen + 3;
              editor.setCursor(newCursor);
            }

            setMarkdown(editor.getMarkdown());
          },
          lang: langSetting,
        }}
      />
      <button onClick={(e) => save()}>Save</button>
    </Fragment>
  );
};
export default MdEditor;
