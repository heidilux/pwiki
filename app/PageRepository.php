<?php namespace jdpike;

use jdpike\CustomMarkdown as Markdown;
use Illuminate\Support\Facades\Storage;

class PageRepository {

    /**
     * Root location for storing directories and .md files
     *
     * @var string
     */
    protected $dataPath;

    /**
     * Set the base path when instantiating
     */
    public function __construct()
    {
        $this->dataPath = base_path() . '/wiki';
    }

    /**
     * Allow for changing the base path
     * Not otherwise implemented yet
     *
     * @param $newPath
     */
    public function setDatapath($newPath)
    {
        $this->dataPath = $newPath;
    }

    /**
     * @return string
     */
    public function getDataPath()
    {
        return $this->dataPath;
    }

    /**
     * Fetch the index page.  It is slightly different from the other pages
     * in that I know what the URL is.
     *
     * @return Page
     */
    public function landingPage()
    {

        $filePath = $this->dataPath . '/index.md';
        if (file_exists($filePath)) {
            $pageHtml = Markdown::defaultTransform(file_get_contents($filePath));
            $pageHtml .= '<hr /><a href="/edit/index.md" class="btn btn-default">Edit page</a>';
        }
        $page = new Page('pWiki', $pageHtml);

        return $page;
    }

    /**
     * Get the parsed .md file, or offer user a link
     * to create the page.
     *
     * @param $filePath
     * @return Page
     */
    public function getPage($filePath)
    {
        $endPoint = $this->getLastSubdirectory($filePath);
        $pagePath = $this->dataPath . '/' . $filePath . '/' . $endPoint . '.md';
        $relativePath = $filePath . '/' . $endPoint . '.md';

        $pageHtml = '<a href="/create/' . $filePath . '" class="btn btn-primary"><i class="fa fa-plus"></i> Create this page</a>';

        if (file_exists($pagePath)) {
            $pageHtml = Markdown::defaultTransform(file_get_contents($pagePath));
            $pageHtml .= '<hr /><a href="/edit/' . $relativePath . '" class="btn btn-default">Edit page</a>';
        }

        $title = $this->deSlugify($endPoint);
        $page = new Page($title, $pageHtml);

        return $page;
    }

    /**
     * Persist the page to the filesystem
     *
     * @param $filePath
     * @param $content
     */
    public function savePage($filePath, $content)
    {
        Storage::put($filePath, $content);
    }

    /**
     * Returns the readable link titles for the list of links
     *
     * @param string $directory
     * @return array
     */
    public function getPageLinks($directory = '')
    {
        $path = $this->getDataPath();

        $directoryNames = $this->getSubdirectories($path);
        //dd($directoryNames);

        $links = $this->createLinkAttributes($directoryNames);

        return $links;
    }

    /**
     * Returns the relative URL for the list of links
     *
     * @param string $directory
     * @return mixed
     */
    public function getLinkUrls($directory = '')
    {
        $relativeUrls = Storage::directories($directory);

        return $relativeUrls;
    }


    /**
     * Convert names into URI slugs
     * Copied from http://cubiq.org/the-perfect-php-clean-url-generator
     *
     * @param $str
     * @return mixed|string
     */
    public function slugify($str)
    {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

        return $clean;
    }

    /**
     * Create readable links from the directory names
     *
     * @param $newLinks
     * @return array
     */
    private function createLinkAttributes($newLinks)
    {
        $links = [];
        foreach ($newLinks as $key => $title) {
            $links[$key]['title'] = $this->deSlugify($title);
        }

        return $links;
    }

    /**
     * Get an array of immediate subdirectories
     *
     * @param $directory
     * @return array
     */
    private function getSubdirectories($path)
    {

        $titles = Storage::directories($path);
        //dd($titles);

        $newLinks = [];
        foreach ($titles as $k => $title) {
            $newLinks[$k] = explode('/', $title);
            $newLinks[$k] = last($newLinks[$k]);
        }

        return $newLinks;
    }

    /**
     * Return the directory name after the last slash.  This
     * will be the page's title.
     *
     * @param $filePath
     * @return array|mixed
     */
    public function getLastSubdirectory($filePath)
    {
        $directories = explode('/', $filePath);
        $endPoint = last($directories);

        return $endPoint;
    }

    /**
     * Convert email-addresses to Email Addresses
     *
     * @param $title
     * @return string
     */
    public function deSlugify($title)
    {
        return ucwords(str_replace('-', ' ', $title));
    }
}