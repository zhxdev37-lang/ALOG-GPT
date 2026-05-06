<?php
/**
 * BlogController - Blog public et articles
 * SEO optimisé, catégories, tags
 */
class BlogController extends BaseController {
    
    public function index(): void {
        $page = max(1, Security::int('page', 'GET', 1));
        $posts = (new BlogPost())->getPublished($page, 12);
        $categories = (new BlogCategory())->getActive();
        $recent = (new BlogPost())->getRecent(5);
        
        $seo = generateSeoMeta([
            'title' => 'Blog',
            'description' => 'Actualités, conseils d\'étude et orientation scolaire au Maroc.'
        ]);
        
        $this->view('blog/index', [
            'posts' => $posts,
            'categories' => $categories,
            'recent' => $recent,
            'seo' => $seo
        ]);
    }
    
    public function show(string $slug): void {
        $post = (new BlogPost())->getBySlug($slug);
        
        if (!$post) {
            http_response_code(404);
            $this->view('public/404');
            return;
        }
        
        $related = (new BlogPost())->getRelated((int)$post['id'], (int)$post['category_id']);
        $categories = (new BlogCategory())->getActive();
        
        $seo = generateSeoMeta([
            'title' => $post['meta_title'] ?: $post['title'],
            'description' => $post['meta_description'] ?: strip_tags($post['excerpt'] ?? ''),
            'image' => $post['og_image'] ?: $post['featured_image'],
            'url' => APP_URL . '/blog/' . $slug,
            'type' => 'article'
        ]);
        
        $this->view('blog/show', [
            'post' => $post,
            'related' => $related,
            'categories' => $categories,
            'seo' => $seo
        ]);
    }
    
    public function category(string $slug): void {
        $page = max(1, Security::int('page', 'GET', 1));
        $posts = (new BlogPost())->getByCategory($slug, $page, 12);
        $categories = (new BlogCategory())->getActive();
        
        $category = (new BlogCategory())->findBy('slug', $slug);
        
        $seo = generateSeoMeta([
            'title' => ($category['name'] ?? $slug) . ' - Blog',
            'description' => $category['meta_description'] ?? 'Articles dans la catégorie ' . ($category['name'] ?? $slug)
        ]);
        
        $this->view('blog/category', [
            'posts' => $posts,
            'category' => $category,
            'categories' => $categories,
            'seo' => $seo
        ]);
    }
    
    public function search(): void {
        $query = Security::input('q', 'GET');
        $page = max(1, Security::int('page', 'GET', 1));
        
        $results = [];
        if (strlen($query) >= 2) {
            $results = (new BlogPost())->search($query, $page);
        }
        
        $seo = generateSeoMeta([
            'title' => 'Recherche: ' . $query,
            'description' => 'Résultats de recherche pour ' . $query
        ]);
        
        $this->view('blog/search', [
            'query' => $query,
            'results' => $results,
            'seo' => $seo
        ]);
    }
}
