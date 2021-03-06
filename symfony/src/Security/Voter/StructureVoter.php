<?php

namespace App\Security\Voter;

use App\Entity\Structure;
use App\Entity\UserInformation;
use App\Manager\UserInformationManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class StructureVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var UserInformationManager
     */
    private $userInformationManager;

    /**
     * @param Security               $security
     * @param UserInformationManager $userInformationManager
     */
    public function __construct(Security $security, UserInformationManager $userInformationManager)
    {
        $this->security               = $security;
        $this->userInformationManager = $userInformationManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Structure) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var UserInformation $userInformation */
        $userInformation = $this->userInformationManager->findOneByUser($token->getUser());
        if (!$userInformation) {
            return false;
        }

        /** @var Structure $structure */
        $structure = $subject;
        if (0 === $structure->getIdentifier()) {
            return false;
        }

        return $userInformation->getStructures()->contains($structure);
    }
}
