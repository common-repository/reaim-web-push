window.myCodeMirror = CodeMirror.fromTextArea(
  document.getElementById("js_editor"),
  {
    lineNumbers: true,
    mode: "javascript",
    theme: "dracula",
  }
);
