# Voters (Symfony like) implementation for Nette 

This authorization system works same as Symfony Voters.

**How to use in Nette application**
- in config.neon register extension
```
extensions:
    authorizator: SklinetNette\Authorizator\Di\AuthorizatorExtension
```

**If you want to create a Voter class**
- create new Voter Class that need implement interface `SklinetNette\Authorizator\Security\Voter\IVoter` or extends class `SklinetNette\Authorizator\Security\Voter`

```
<?php
namespace App\Security\Voter;

use App\Entity\Article;
use SklinetNette\Authorizator\Security\Voter\Voter;

/**
 * Class Article
 *
 * @package App\Security\Voter
 */
class ArticleVoter extends Voter
{
    const EDIT = 'edit';
    const CREATE = 'create';

    protected function supports(string $attribute, $subject): bool
    {
        if ( ! in_array($attribute, [self::EDIT])) {
            return false;
        }

        if ( ! $subject instanceof Article) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, \Nette\Security\User $securityUser): bool
    {
        //or you can get user entity with entity manager
        if ( ! $securityUser->getId()) {
            return false;
        }

        ////////////////////////////////////////////////////////
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            default:
                return false;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param  \App\Entity\Article  $article
     * @param  \Nette\Security\User $securityUser
     *
     * @return bool
     */
    private function canEdit(Article $article, \Nette\Security\User $securityUser): bool
    {
        if ($securityUser->getId() == $article->getUser()->getId()) {
            return true;
        }

        return false;
    }
}
```

- register voter as a service in config.neon

```
services:
   ArticleVoter:
      class: App\Security\Voter\ArticleVoter
```

- in presenter use trait `SklinetNette\Authorizator\Traits\AuthorizatorTrait` or service `SklinetNette\Authorizator\Authorizator`
- both classes have method isGranted
- method `isGranted(string $attribute, mixed $subject)` accepts two arguments. First argument attribute -> what a user can do with a resource (edit, create, etc) and second argument is a resource -> resource can be anything (doctrine entity, string, array, object, etc)
```
class ArticlePresenter extends \App\Presenters\BasePresenter
{
    use AuthorizatorTrait;
    
    ...

    public function actionEdit($id)
    {
        /** @var \App\Entity\Article $article */
        $article = $this->em
            ->getRepository(Article::class)
            ->findOneBy(['id' => (int)$id]);

        if ($this->isGranted('edit', $article)) {
            $this->error();
        }
       
        ...
    }
}
```

- in Latte template you can use function isGranted that accepts same arguments as trait method isGranted

```
<a n:if="isGranted('edit', $article)" role="button" class="btn">
    ...
</a>
```

