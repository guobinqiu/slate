# How to deploy your API Doc

---

1. Install slate web application on your vagrant by running `make api-install` command.

2. Update `index.html.md` file located at `app/docs/api/` directory. Also see [Markdown Syntax](https://github.com/lord/slate/wiki/Markdown-Syntax)

3. Run command `make api-deploy` at your project root directory.

4. Preview from the following url: `http://<Your Vagrant IP Address>:4567`