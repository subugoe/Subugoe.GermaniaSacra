<?php
namespace Subugoe\GermaniaSacra\Controller;

use TYPO3\Flow\Annotations as Flow;
use Subugoe\GermaniaSacra\Domain\Model\Klosterstandort;

class KlosterstandortController extends AbstractBaseController
{
    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterstandortRepository
     */
    protected $klosterstandortRepository;

    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\KlosterRepository
     */
    protected $klosterRepository;

    /**
     * @Flow\Inject
     * @var \Subugoe\GermaniaSacra\Domain\Repository\OrtRepository
     */
    protected $ortRepository;

    /**
     */
    public function indexAction()
    {
        $this->view->assign('klosterstandorts', $this->klosterstandortRepository->findAll());
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Klosterstandort $klosterstandort
     */
    public function showAction(Klosterstandort $klosterstandort)
    {
        $this->view->assign('klosterstandort', $klosterstandort);
    }

    /**
     */
    public function newAction()
    {
        $this->view->assign('klosters', $this->klosterRepository->findAll());
        $this->view->assign('orts', $this->ortRepository->findAll());
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Klosterstandort $newKlosterstandort
     */
    public function createAction(Klosterstandort $newKlosterstandort)
    {
        $this->klosterstandortRepository->add($newKlosterstandort);
        $this->addFlashMessage('Created a new klosterstandort.');
        $this->redirect('index');
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Klosterstandort $klosterstandort
     */
    public function editAction(Klosterstandort $klosterstandort)
    {
        $this->view->assign('klosterstandort', $klosterstandort);
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Klosterstandort $klosterstandort
     */
    public function updateAction(Klosterstandort $klosterstandort)
    {
        $this->klosterstandortRepository->update($klosterstandort);
        $this->addFlashMessage('Updated the klosterstandort.');
        $this->redirect('index');
    }

    /**
     * @param \Subugoe\GermaniaSacra\Domain\Model\Klosterstandort $klosterstandort
     */
    public function deleteAction(Klosterstandort $klosterstandort)
    {
        $this->klosterstandortRepository->remove($klosterstandort);
        $this->addFlashMessage('Deleted a klosterstandort.');
        $this->redirect('index');
    }
}
