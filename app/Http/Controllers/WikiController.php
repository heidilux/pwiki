<?php namespace jdpike\Http\Controllers;

use jdpike\Http\Requests;
use jdpike\Http\Requests\CreateLink;
use jdpike\Http\Requests\DeleteLink;
use jdpike\Http\Requests\SavePage;
use jdpike\PageRepository;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class WikiController extends Controller {

    protected $pages;

    /**
     * Users must be authenticated in order to proceed
     *
     * @param PageRepository $pages
     */
    public function __construct(PageRepository $pages)
    {
        $this->middleware('auth');
        $this->pages = $pages;
    }

    /**
     * Slightly different method when I know the URI is '/'
     *
     * @return \Illuminate\View\View
     */
    public function showLanding()
    {
        $filePath = '/';
        $page = $this->pages->landingPage();
        $links = $this->pages->getPageLinks();
        $urls = $this->pages->getLinkUrls();

        Session::put('url', '/');

        return view('wiki', compact('filePath', 'links', 'urls', 'page'));
    }


    /**
     * Fetch the page and link(s) content to send to the view
     *
     * @param      $page
     * @param null $nextPage
     * @return \Illuminate\View\View
     */
    public function showPage($page = null, $nextPage = null)
    {
        $filePath = ($nextPage) ? $page . '/' . $nextPage : $page;
        $links = ($nextPage) ? $this->pages->getPageLinks($filePath) : $this->pages->getPageLinks($page);
        $urls = ($nextPage) ? $this->pages->getLinkUrls($filePath) : $this->pages->getLinkUrls($page);
        $page = $this->pages->getPage($filePath);

        Session::put('url', $filePath);

        return view('wiki', compact('filePath', 'links', 'urls', 'page'));
    }

    /**
     * Show user the 'create page' page
     *
     * @return \Illuminate\View\View
     */
    public function showCreatePage()
    {
        return view('create');
    }


    /**
     * Create the md file and save to the filesystem
     *
     * @param SavePage $request
     * @param null       $page
     * @param null       $nextPage
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createPage(SavePage $request, $page = null, $nextPage = null)
    {
        $content = $request->all();

        $directoryPath = ($nextPage) ? $page . '/' . $nextPage : $page;
        $filePath = $directoryPath . '/' . $this->pages->getLastSubdirectory($directoryPath) . '.md';

        if (! Storage::exists($filePath))
        {
            $this->pages->savePage($filePath, $content['content']);
        }

        return redirect($directoryPath);
    }

    /**
     * Show user the 'page-edit' page pre-populated with existing markdown
     *
     * @param null $page
     * @param null $nextPage
     * @return \Illuminate\View\View
     */
    public function showEditPage($page = null, $nextPage = null)
    {
        $filePath = ($nextPage) ? $page . '/' . $nextPage : $page;

        $content = Storage::get($filePath);

        return view('edit', compact('content', 'filePath'));
    }

    /**
     * Save edited page, overwriting existing markdown file
     *
     * @param SavePage $request
     * @param null     $page
     * @param null     $nextPage
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editPage(SavePage $request, $page = null, $nextPage = null)
    {
        $content = $request->all();
        $directoryPath = ($nextPage) ? $page . '/' . $nextPage : $page;

        if ($nextPage)
        {
            $filePath = $directoryPath;
        } else {
            $filePath = 'index.md';
        }

        if (Storage::exists($filePath))
        {
            $this->pages->savePage($filePath, $content['content']);
        }

        return redirect(Session::get('url'));

    }

    /**
     * Delete the .md file
     *
     * @param $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deletePage($page)
    {
        if (Storage::exists($page)) {
            Storage::delete($page);
        }

        return redirect(Session::get('url'));
    }

    /**
     * Create a new Link (subdirectory)
     *
     * @param CreateLink $request
     * @param null       $directory
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createLink(CreateLink $request, $directory = null)
    {
        $newLink = $this->pages->slugify($request->get('link'));
        if ($directory)
        {
            if ($directory != Storage::directories($directory))
            {
                Storage::makeDirectory($directory . '/' . $newLink);
            }
        } else {
            Storage::makeDirectory($newLink);
        }

        return redirect(Session::get('url'));
    }

    /**
     * Delete a link (subdirectory) and all of its contents (careful!!)
     *
     * @param DeleteLink $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteLinks(DeleteLink $request)
    {
        $checked = $request->except('_token', 'path');
        foreach ($checked as $link => $v)
        {
            var_dump($link . ' to be deleted');
            Storage::deleteDirectory('/' . $link);
            var_dump($link . ' deleted...');
        }

        return redirect(Session::get('url'));
    }

}
