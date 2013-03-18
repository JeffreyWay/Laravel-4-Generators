<?php

class {{name}} extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = '{{tableName}}';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * The variables accessible for Input::only()
     *
     * @var array
     */
    public $accept = array();

}