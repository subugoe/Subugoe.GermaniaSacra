<?php
namespace Subugoe\GermaniaSacra\Controller;

use Subugoe\GermaniaSacra\Queue\SolrUpdateJob;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;

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
     * @Flow\Inject
     * @var \TYPO3\Jobqueue\Common\Job\JobManager
     */
    protected $jobManager;

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
        if ($this->securityContext->canBeInitialized()) {
            $account = $this->securityContext->getAccount();
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
        $this->jobManager->queue('solr', $job);
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
