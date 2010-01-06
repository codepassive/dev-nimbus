<?php
/**
 *  PHP Git
 *
 *  Pure-PHP class to read GIT repositories. It allows to
 *  perform read-only operations such as get commit history
 *  get files, get branches, and so forth.
 *
 *  PHP version 5
 *
 *  @category VersionControl
 *  @package  PHP-Git
 *  @author   C�sar D. Rodas <crodas@member.fsf.org>
 *  @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 *  @link     http://cesar.la/git
 */
/**
 *  Git Class
 *
 *  @category VersionControl
 *  @package  PHP-Git
 *  @author   C�sar D. Rodas <crodas@member.fsf.org>
 *  @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 *  @link     http://cesar.la/git
 */
class Git extends GitBase
{
    private $_cache;

    // {{{ __construct 
    /**
     *  Class constructor
     *
     *  @param string $path Git path repo.
     *
     *  @return null
     */
    final function __construct($path='')
    {
        if ($path=='') {
            return;
        }
        $this->setRepo($path);
    }
    // }}}

    // {{{ getBranches
    /**
     *  Returns all the branches
     *
     *  @return Array list of branches
     */
    function getBranches()
    {
        return array_keys($this->branch);
    }
    // }}}

    // {{{ getHistory
    /**
     *  Returns all commit history on a given branch.
     *
     *  @param string $branch Branch name
     *  @param int    $limit  History limitation
     *
     *  @return mixed Array with commits history or exception
     */
    function getHistory($branch,$limit=10)
    {
        if ($this->branch[$branch] === false) {
            $this->throwException("$branch is not a valid branch");
        }
       
        $object_id = $this->branch[$branch];
        $history   = array();
        $e         = 0;
        do {   
            $commit = $this->getObject($object_id, $type);
            if ($commit == false || $type != OBJ_COMMIT) {
                $this->throwException("Unexpected datatype");
            }
            $commit["id"] = $object_id; 
            $history[]    = $commit;
            if (!isset($commit["parent"]) || ++$e == $limit) {
                break;
            }
            $object_id = $commit["parent"];
        } while (1);
        return $history;
    }    
    // }}} 

    // {{{ getTags
    /**
     *  Get Tags
     *
     *  Returns the avaliable tags on a git repo.
     *
     *  @return array All tags avaliable
     */
    function getTags()
    {
        $tags = $this->getRefInfo('tags');
        if (count($tags) == 0) {
            return array();
        }
        return array_combine(array_values($tags),
                array_keys($tags));
            
    }
    // }}}

    // {{{ getCommit
    /**
     *  Get commit list of files.
     *
     *  @param string $id Commit Id.
     *
     *  @return mixed Array with commit's files or an exception
     */
    function getCommit($id)
    {
        $obj = $this->getObject($id, $type);
        if ($obj === false || $type != OBJ_COMMIT) {
            $this->throwException("$id is not a valid commit");
        }
        $obj['Tree'] = $this->getObject($obj['tree']);
        return $obj;
    }
    // }}}

    //{{{ getTag
    /** 
     *  Get information about a tag.
     *
     *  @param string $id Tag commit id.
     *
     *  @return object Object.
     */
    function getTag($id)
    {
        $obj = $this->getObject($id, $type);
        if ($obj === false) {
            $this->throwException("There is not object $id");
        }
        switch ($type) {
        case OBJ_TAG:
            break;
        case OBJ_COMMIT:
            /* A tag can be a commit, so simulate it */
            $nobj = array(
                "object"  => $id,
                "type"    => "commit",
                "tag"     => "",
                "tagger"  => $obj["committer"],
                "comment" => $obj["comment"],
                "Tree"    => $this->getObject($obj['tree'])
            );
            $obj  = $nobj;
            break;
        default:
            $this->throwException("Unexpected ($type) object type.");
        }
        return $obj;
    }
    //}}}

    // {{{ getFile
    /**
     *  Returns a parsed object from the repo.
     *
     *  @param string $id    Sha1 object id.
     *  @param int    &$type Object type
     *
     *  @return mixed file content or an exception  
     */
    function getFile($id,&$type=null)
    {
        $obj = $this->getObject($id, $type);
        return $obj;
        if ($obj === false) {
            if ( sha1("blob 0".chr(0)) == $id) {
                return "";
            }
            $this->throwException("Object $id doesn't exists");
        }
        switch ($type) {
        case OBJ_TREE:
            $obj = $this->parseTreeObject($obj);
            break;
        case OBJ_COMMIT:
            $obj = $this->parseCommitObject($id);
            break;
        }
        return $obj;
    }
    // }}} 

    // {{{ getCommitDiff
    /**
     *  Get a diff form the $id commit and the
     *  previous commit.
     *
     *  @param string $id Commit id.
     *
     *  @return array Array with changes
     */
    function getCommitDiff($id)
    {
        $tree  = $this->getCommit($id); 
        $tree1 = $this->getCommit($tree["parent"]);

        return $this->getTreeDiff($tree['tree'], $tree1['tree']);
    } 
    // }}}

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */

?>
