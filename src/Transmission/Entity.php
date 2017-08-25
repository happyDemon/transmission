<?php

namespace HappyDemon\Transmission;


class Entity
{
    protected $requires = [];

    protected $properties = [];
    /**
     * @var Transmission
     */
    protected $transmission;

    /**
     * Entity constructor.
     *
     * @param Transmission $transmission
     * @param array        $fields
     *
     * @throws EntityException
     */
    public function __construct( Transmission $transmission, $fields )
    {
        // Make sure the required fields are present
        if(count($this->requires) > 0)
        {
            $fields = (array) $fields;
            foreach($this->requires as $field)
            {
                if(!isset($fields[$field])) throw new EntityException($field . ' is required for ' . __CLASS__);
            }
        }

        $this->properties = $fields;
        $this->transmission = $transmission;
    }

    public function update( $fields )
    {
        $this->properties = array_merge($this->properties, $fields);
        return $this;
    }

    public function __get( $property )
    {
        return $this->properties[$property] ?: null;
    }

    public function toArray()
    {
        return $this->properties;
    }
}