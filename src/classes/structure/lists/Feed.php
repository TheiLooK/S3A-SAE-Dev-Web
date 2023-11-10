<?php
declare(strict_types=1);
namespace touiteur\app\structure\lists;

use touiteur\app\exception\InvalidPropertyNameException;
use touiteur\app\structure\touite\Touite;

class Feed {
    const NBPARPAGEFEED = 7;
    const LISTETOUITES = 1;
    const LISTETOUITESPERSONNE = 2;
    const LISTETOUITESFOLLOWED = 3;
    const LISTETOUITESTAG = 4;
    const LISTETOUITESLIKE = 5;

    protected iterable $list;
    protected int $type;
    protected ?string $user;
    protected ?string $tag;
    protected int $nbTouiteMax;
    protected string $action;
    protected ?string $like;

    public function __construct(int $type, string $action, ?string $user, ?string $tag, ?string $like) {
        $this->list = [];
        $this->type = $type;
        $this->user = $user;
        $this->tag = $tag;
        $this->action = $action;
        $this->like = $like;
        $this->nbTouiteMax = $this->getNbTouite();
    }

    /**
     * Method used to add a touite to the list
     * @param Touite $touite
     * @return void
     */
    public function ajouterTouite(Touite $touite):void{
        $this->list[] = $touite;
    }

    /**
     * Method used to remove a touite to the list
     * @param int $indice the index of the touite in the list
     * @return void
     */
    public function suprimerTouite(int $indice):void{
        unset($this->list[$indice]);
    }

    /**
     * Method used to get an argument
     * @param String $arg the name of the argument
     * @return mixed the argument if it exists
     * @throws InvalidPropertyNameException
     */
    public function __get(String $arg): mixed {
        if(property_exists($this, $arg)) return $this->$arg;
        throw new InvalidPropertyNameException("$arg: invalid property");
    }

    public function getListe(int $nbPage): void {
        switch ($this->type) {
            case self::LISTETOUITES:
                $this->getListeTouite($nbPage);
                break;
            case self::LISTETOUITESPERSONNE:
                $this->getListeTouitePersonne($nbPage, $this->user);
                break;
            case self::LISTETOUITESFOLLOWED:
                $this->getListeTouiteFollowed($nbPage, $this->user);
                break;
            case self::LISTETOUITESTAG:
                $this->getListeTouiteTag($nbPage, $this->tag);
                break;
            case self::LISTETOUITESLIKE:
                $this->getListeTouiteLike($nbPage, $this->like);
                break;
        }
    }

    public function getNbTouite(): int {
        switch ($this->type) {
            case self::LISTETOUITES:
                return $this->getNbTouiteTouite();
            case self::LISTETOUITESPERSONNE:
                return Feed::getNbTouitePersonne($this->user);
            case self::LISTETOUITESFOLLOWED:
                return $this->getNbTouiteFollowed($this->user);
            case self::LISTETOUITESTAG:
                return Feed::getNbTouiteTag($this->tag);
            case self::LISTETOUITESLIKE:
                return $this->getNbTouiteLike($this->like);
            default:
                return 0;
        }
    }

    private function getListeTouite(int $nbPage): void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * FROM touite t INNER JOIN users u ON t.email = u.email 
                           ORDER BY dateTouite DESC
                           LIMIT ?, ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([($nbPage-1)*self::NBPARPAGEFEED, self::NBPARPAGEFEED]);

        while ($data = $resultset->fetch(\PDO::FETCH_ASSOC)){
            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

    private function getListeTouitePersonne(int $nbPage, string $user): void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * FROM touite t INNER JOIN users u ON t.email = u.email
                                        WHERE u.username = ?
                                        ORDER BY dateTouite DESC
                                        LIMIT ?, ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$user, ($nbPage-1)*self::NBPARPAGEFEED, self::NBPARPAGEFEED]);

        while ($data = $resultset->fetch(\PDO::FETCH_ASSOC)){
            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

    private function getListeTouiteFollowed(int $nbPage, string $user): void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT DISTINCT tt.idTouite
                    FROM touiteToTag tt
                    INNER JOIN followTag ft ON tt.idTag = ft.idTag
                    WHERE ft.emailSuiveur = ?
                    UNION
                    SELECT DISTINCT t.idTouite
                    FROM touite t
                    INNER JOIN follow f ON t.email = f.emailSuivi
                    WHERE f.emailSuiveur = ?
                    ORDER BY idTouite DESC
                    LIMIT ?, ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$user, $user, ($nbPage-1)*self::NBPARPAGEFEED, self::NBPARPAGEFEED]);

        while ($data = $resultset->fetch(\PDO::FETCH_ASSOC)){
            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

    private function getListeTouiteTag(int $nbPage, string $tag): void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * FROM touite t2 INNER JOIN touiteToTag t ON t2.idTouite = t.idTouite 
                                        INNER JOIN tag tag ON t.idTag = tag.idTag
                                        WHERE tag.libelle = ?
                                        ORDER BY dateTouite DESC
                                        LIMIT ?, ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$tag, ($nbPage-1)*self::NBPARPAGEFEED, self::NBPARPAGEFEED]);

        while ($data = $resultset->fetch(\PDO::FETCH_ASSOC)){
            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

    private function getListeTouiteLike(int $nbPage, string $like): void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * FROM touite WHERE texte LIKE ?
                                        ORDER BY dateTouite DESC
                                        LIMIT ?, ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute(["%$like%", ($nbPage-1)*self::NBPARPAGEFEED, self::NBPARPAGEFEED]);

        while ($data = $resultset->fetch(\PDO::FETCH_ASSOC)){
            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

    private function getNbTouiteTouite(): int {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT COUNT(*) AS nbTouite FROM touite";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute();
        $data = $resultset->fetch(\PDO::FETCH_ASSOC);
        return $data['nbTouite'];
    }

    public static function getNbTouitePersonne(string $user): int {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT COUNT(*) AS nbTouite FROM touite t INNER JOIN users u ON t.email = u.email WHERE u.username = ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$user]);
        $data = $resultset->fetch(\PDO::FETCH_ASSOC);
        return $data['nbTouite'];
    }

    private function getNbTouiteFollowed(string $user): int {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT COUNT(*) as nbTouite
                    FROM touiteToTag tt
                    INNER JOIN followTag ft ON tt.idTag = ft.idTag
                    WHERE ft.emailSuiveur = ?
                    UNION
                    SELECT COUNT(*) as nbTouite
                    FROM touite t
                    INNER JOIN follow f ON t.email = f.emailSuivi
                    WHERE f.emailSuiveur = ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$user, $user]);
        $data = $resultset->fetch(\PDO::FETCH_ASSOC);
        return $data['nbTouite'];
    }

    public static function getNbTouiteTag(string $tag): int {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT COUNT(*) AS nbTouite FROM touiteToTag t INNER JOIN tag tag ON t.idTag = tag.idTag WHERE tag.libelle = ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$tag]);
        $data = $resultset->fetch(\PDO::FETCH_ASSOC);
        return $data['nbTouite'];
    }

    public static function getNbFollowersTag(string $tag): int {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT COUNT(*) AS nbFollowers FROM followTag ft INNER JOIN tag t ON ft.idTag = t.idTag WHERE t.libelle = ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$tag]);
        $data = $resultset->fetch(\PDO::FETCH_ASSOC);
        return $data['nbFollowers'];
    }

    private function getNbTouiteLike(string $like): int {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT COUNT(*) AS nbTouite FROM touite WHERE texte LIKE ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute(["%$like%"]);
        $data = $resultset->fetch(\PDO::FETCH_ASSOC);
        return $data['nbTouite'];
    }
}