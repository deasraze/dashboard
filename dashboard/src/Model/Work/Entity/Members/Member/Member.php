<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use App\Model\Work\Entity\Members\Group\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="work_members_members")
 */
class Member
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="work_members_member_id")
     */
    private Id $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Members\Group\Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    private Group $group;
    /**
     * @ORM\Embedded(class="Name")
     */
    private Name $name;
    /**
     * @ORM\Column(type="work_members_member_email")
     */
    private Email $email;
    /**
     * @ORM\Column(type="work_members_member_status", length=16)
     */
    private Status $status;
}
