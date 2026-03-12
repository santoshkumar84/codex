document.addEventListener('DOMContentLoaded', () => {
  const codeArea = document.getElementById('code-editor');
  if (!codeArea) return;

  const editor = CodeMirror.fromTextArea(codeArea, {
    lineNumbers: true,
    mode: 'text/x-c++src',
    theme: 'default',
    indentUnit: 4,
    tabSize: 4,
  });

  const langSelect = document.getElementById('language');
  const modeMap = {
    c: 'text/x-csrc',
    cpp: 'text/x-c++src',
    java: 'text/x-java',
    python: 'python',
    php: 'application/x-httpd-php'
  };

  const boilerplate = {
    c: '#include <stdio.h>\nint main(){\n    return 0;\n}',
    cpp: '#include <bits/stdc++.h>\nusing namespace std;\nint main(){\n    return 0;\n}',
    java: 'import java.util.*;\npublic class Main {\n    public static void main(String[] args) {\n    }\n}',
    python: 'def main():\n    pass\n\nif __name__ == "__main__":\n    main()',
    php: '<?php\nfunction main(): void {\n}\n\nmain();'
  };

  langSelect?.addEventListener('change', () => {
    const lang = langSelect.value;
    editor.setOption('mode', modeMap[lang] || 'text/plain');
    if (!editor.getValue().trim()) {
      editor.setValue(boilerplate[lang] || '');
    }
  });

  const submissionForm = document.getElementById('submission-form');
  submissionForm?.addEventListener('submit', () => {
    codeArea.value = editor.getValue();
  });
});
