# How to deploy your API Doc

---

1. Install slate web application on your vagrant by running `make apidoc-deploy` command.

2. Update `index.html.md` file located at `docs/api/` directory. Also see [Markdown Syntax](https://github.com/lord/slate/wiki/Markdown-Syntax)

3. Run command `make apidoc-server-startup` at your project root directory.

4. Preview from the following url: `http://<Your Vagrant IP Address>:4567`

5. Find {pid} of preview server by `ps -ef |grep middleman` and `kill {pid}` to stop preview server.