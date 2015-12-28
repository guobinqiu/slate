<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HobbyListRepository extends EntityRepository
{

    /**
     * @param integer $id
     * @return String hobby name
     */
    public function getHobbyName($id)
    {
        $em = $this->getEntityManager();
        $hobby = $em->getRepository('JiliApiBundle:HobbyList')->find($id);
        return $hobby ? $hobby->getHobbyName() : '';
    }

    /**
     * @param String $user_hobby '1,2,3'
     * @return String $user_hobby_name '上网,游戏'
     */
    public function getUserHobbyName($user_hobby)
    {
        // user_hobby is empty
        if (empty($user_hobby)) {
            return '';
        }

        // get user hobby name array
        $user_hobby_arr = explode(',', $user_hobby);
        foreach ($user_hobby_arr as $key => $value) {
            $hobby_name = $this->getHobbyName($value);
            if ($hobby_name) {
                $user_hobby_names[] = $hobby_name;
            }
        }

        // get user hobby name string
        $user_hobby_name = '';
        if (is_array($user_hobby_names)) {
            $user_hobby_name = implode(',', $user_hobby_names);
        }
        return $user_hobby_name;
    }
}
