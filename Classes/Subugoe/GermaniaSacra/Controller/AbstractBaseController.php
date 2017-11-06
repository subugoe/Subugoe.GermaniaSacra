<?php
namespace Subugoe\GermaniaSacra\Controller;

use Flowpack\JobQueue\Common\Job\JobManager;
use Subugoe\GermaniaSacra\Queue\SolrUpdateJob;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Security\Context;

/**
 * An action controller with base functionality for all action controllers
 */
abstract class AbstractBaseController extends ActionController
{
    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\BearbeiterRepository
     */
    protected $bearbeiterRepository;

    /**
     * @var \TYPO3\Flow\Security\Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @var \Subugoe\GermaniaSacra\Domain\Model\Bearbeiter
     */
    protected $bearbeiterObj;

    /**
     * @var \TYPO3\Flow\Cache\CacheManager
     * @Flow\Inject
     */
    protected $cacheManager;

    /**
     * @var \TYPO3\Flow\Cache\Frontend\FrontendInterface
     */
    protected $cacheInterface;

    /**
     * @var bool
     */
    protected $dumpLogFileExists;

    /**
     * Initializes the controller before invoking an action method.
     *
     */
    public function initializeAction()
    {
        $securityContext = new Context();
        if ($securityContext->canBeInitialized()) {
            $account = $securityContext->getAccount();
            $this->bearbeiterObj = $this->bearbeiterRepository->findOneByAccount($account);
        }
        $this->cacheInterface = $this->cacheManager->getCache('GermaniaSacra_GermaniaCache');
    }

    /**
     * @param string $entity
     */
    protected function clearCachesFor($entity)
    {
        if ($this->cacheInterface->has($entity)) {
            $this->cacheInterface->remove($entity);
        }

        $this->clearKlosterCache();

        $this->triggerSolrUpdate();
    }

    /**
     */
    protected function triggerSolrUpdate()
    {
        $job = new SolrUpdateJob('solr');
        $jobManager = new JobManager();
        $jobManager->queue('solr', $job);
    }

    /**
     */
    protected function clearKlosterCache()
    {
        if ($this->cacheInterface->has('kloster')) {
            $this->cacheInterface->remove('kloster');
        }
    }
}
