<?php

class ArticleAgregator
{
    protected $articles = [];

    public function __construct($port, $username, $password, $database)
    {
        // En partant de l'idée que la database alltricks_test existe déjà ! :)
        $dsn = "mysql:host={$port};dbname={$database}";
        try {
            $this->pdo = new PDO($dsn, $username, $password);
        } catch(PDOException $e) {
            echo 'Connexion échoué : ' . $e->getMessage();
            exit();
        }

        $this->createTables();
    }
    protected function createTables()
    {
        $query = "CREATE TABLE IF NOT EXISTS source (
            id int NOT NULL auto_increment,
            name varchar(255),
            PRIMARY KEY(id)
            );
            CREATE TABLE IF NOT EXISTS article (
                id int NOT NULL auto_increment,
                source_id int NOT NULL,
                name varchar(255),
                content BLOB,
                PRIMARY KEY(id)
            );
            INSERT INTO source VALUES (1, 'src-1');
            INSERT INTO source VALUES (2, 'src-2');
            INSERT INTO article VALUES (1, 1, 'Article 1', 'Lorem ipsum dolor sit amet 1');
            INSERT INTO article VALUES (2, 2, 'Article 2', 'Lorem ipsum dolor sit amet 2');
            INSERT INTO article VALUES (3, 2, 'Article 3', 'Lorem ipsum dolor sit amet 3');
            INSERT INTO article VALUES (4, 1, 'Article 4', 'Lorem ipsum dolor sit amet 4');"
        ;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }

    public function appendDatabase()
    {
        $querySelectProducts = "SELECT 
            article.name, 
            source.name AS sourceName, 
            content 
            FROM article 
            INNER JOIN source 
            ON source.id = article.source_id"
        ;

        $stmt = $this->pdo->prepare($querySelectProducts);
        $stmt->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $article = new \stdClass();

            $article->name = $row["name"];
            $article->sourceName = $row["sourceName"];
            $article->content = $row["content"];

            $this->articles[] = $article;
        }
    }

    public function appendRss($name, $feedUrl)
    {
        // Get the content of url and instantiate in SimpleXMlElement
        $content = file_get_contents($feedUrl);
        $flux = new \SimpleXmlElement($content);
        
        foreach($flux->channel->item as $item) {
            $article = new \stdClass();
            
            $article->name = $item->title;
            $article->sourceName = $name;
            $article->content = $item->link;

            $this->articles[] = $article;
        }
    }

    public function getArticles()
    {
        return $this->articles;
    }
}

/**
 * host, username, password, database name 
 * password = local personnal password
 */
$a = new ArticleAgregator('localhost', 'root', '', 'alltricks_test');

/**
 * Récupère les articles de la base de données, avec leur source.
 */
$a->appendDatabase();

/**
 * Récupère les articles d'un flux rss donné
 * source name, feed url
 */
$a->appendRss('Le Parisien', 'http://www.leparisien.fr/actualites-a-la-une.rss.xml');
$a->appendRss('Le Monde',    'http://www.lemonde.fr/rss/une.xml');

foreach ($a->getArticles() as $article) {
    echo sprintf("<h2>%s</h2>'\n'<em>%s</em>'\n'<p>%s</p>'\n'",
        $article->name,
        $article->sourceName,
        $article->content
    );
}
