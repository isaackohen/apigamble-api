# Overview

---

- [Overview](#welcome)
 
<a name="welcome"></a>
## 👋 Welcome

<p>Welcome to APIGamble documentation, we will go through the start-up phase briefly. Please note your account manager will be there to guide you through the process. </p>


<blockquote>
<p>{info} Make sure to have your API Key read from your account, as is used in all services. Each service has independant API key to auth.<a href="../manage/"><code>Admin Panel</code></a></p>
</blockquote>

<larecipe-swagger endpoint="/api/slots/listGames"></larecipe-swagger>



<h2>Features</h2>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-book"></larecipe-badge> Markdown / WYSIWYG Editor
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-book"></larecipe-badge> Dashboard
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-paper-plane"></larecipe-badge> SEO Support
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-link"></larecipe-badge> API
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-shield"></larecipe-badge> Authorization
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-users"></larecipe-badge> Multi Users
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-shield"></larecipe-badge> Security
    <larecipe-progress :striped="true" :animated="true" type="success" :value="99"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa  fa-clock-o"></larecipe-badge> Reading Time Estimation
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-comment"></larecipe-badge> Forum Support
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-bar-chart"></larecipe-badge> Google Analytics
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa fa-share"></larecipe-badge> Social Share
    <larecipe-progress :striped="true" :animated="true" type="success" :value="100"></larecipe-progress>
</larecipe-card>
<larecipe-card>
    <larecipe-badge type="success" circle class="mr-3" icon="fa  fa-user"></larecipe-badge> Responsive UI
    <larecipe-progress :striped="true" :animated="true" type="success" :value="99"></larecipe-progress>
</larecipe-card>
<p><a name="credits"></a></p>
<h2>Credits</h2>
<p>Blogged package uses internally some open-source third-party libraries/packages, many thanks to the web community:</p>
<ul>
<li><a href="https://creative-tim.com">Creative Tim</a> - Awesome people, thanks for Argon library.</li>
<li><a href="https://laravel.com">Laravel</a> - Open source full-stack framework.</li>
<li><a href="https://getbootstrap.com">Bootstrap 4</a> - Open source front end framework.</li>
<li><a href="https://vuejs.org/">VueJs</a> - The Progressive JavaScript Framework.</li>
<li><a href="https://github.com/erusev/parsedown-extra">erusev/parsedown-extra</a> - PHP markdown parser.</li>
<li><a href="https://github.com/symfony/dom-crawler">symfony/dom-crawler</a> - Dom manipulation.</li>
<li><a href="https://github.com/sebastianbergmann/phpunit">phpunit/phpunit</a> - PHP unit testing library.</li>
<li><a href="https://github.com/orchestral/testbench">orchestra/testbench</a> - Unit test package for Laravel packages.</li>
<li><a href="https://github.com/mewebstudio/Purifier">mews/purifier</a> - HTMLPurifier for Laravel 5.</li>
</ul>

<h3>URL Params</h3>
<pre><code class="language-text">None</code></pre>
<h3>Data Params</h3>
<pre><code class="language-json">{
    "grant_type"    : "password",
    "client_id"     : "2",
    "client_secret" : "{client_secret}",
    "username"      : "string|email",
    "password"      : "string",
    "scope"         : "*"
}</code></pre>
<blockquote>
<p>{info} The <code>client_secret</code> is the token that the server needs in order to auth the request. See server config page for more details.</p>
</blockquote>
<p>For the <code>dev</code> server the</p>
<pre><code class="language-php">$client_secret = dev_client_secret_here</code></pre>
<p>For the <code>production</code> server the</p>
<pre><code class="language-php">$client_secret = production_client_secret_here</code></pre>
<blockquote>
<p>{primary} Login request example with development server</p>
</blockquote>
<pre><code class="language-json">{
    "grant_type"    : "password",
    "client_id"     : "2",
    "client_secret" : "dev_client_secret_here",
    "username"      : "test@test.com",
    "password"      : "secret",
    "scope"         : "*"
}</code></pre>
<blockquote>
<p>{success} Success Response</p>
</blockquote>
<p>Code <code>200</code></p>
<p>Content</p>
<pre><code class="language-json">{
  "token_type"    : "Bearer",
  "expires_in"    : "integer",
  "access_token"  : "string|token",
  "refresh_token" : "string|token"
}</code></pre>
<blockquote>
<p>{danger} Error Response</p>
</blockquote>
<p>Code <code>422</code></p>
<p>Reason <code>put description here</code></p>
<p>Content</p>
<pre><code class="language-json">...</code></pre>
<larecipe-newsletter></larecipe-newsletter>
