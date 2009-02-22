<?php

class UploadableBehavior extends ModelBehavior
{
    public $extensions = array (
        'audio/mpeg' => 'mp3',
        'image/png' => 'png',
        'image/jpeg' => 'jpeg',
    );


    public function setup (&$model, $config)
    {
        if (isset($config['destination']))
            $this->destination = $config['destination'];

        elseif (Configure::read($model->name . 'Path'))
            $this->destination = Configure::read($model->name . 'Path');
            
        else
            $this->destination = WWW_ROOT . 'files';
    }


    public function beforeSave (&$model)
    {
        $model->data[$model->name]['mime'] = $model->data[$model->name]['content']['type'];
    }


    public function tryUploadIt (&$model, $data)
    {
        $data = array_shift($data);

        $ext = '';
        if (isset($this->extensions[$data['type']]))
            $ext .= '.' . $this->extensions[$data['type']];

        do $model->data[$model->name]['filename'] = md5(mt_rand()) . $ext;
        while (file_exists($this->destination . $model->data[$model->name]['filename']));

        if (move_uploaded_file($data['tmp_name'], $this->destination . $model->data[$model->name]['filename']))
            return true;

        return false;
    }


	public function isUploaded (&$model, $data)
	{
		$data = array_shift($data);

		if ($data['error'] || empty($data['tmp_name']) || $data['tmp_name'] == 'none')
			return false;

		return is_uploaded_file($data['tmp_name']);
	}


	public function isMP3 (&$model, $data)
	{
		$data = array_shift($data);

        switch ($data['type'])
        {
            case 'audio/mpeg':
                return true;
            default:
                return false;
        }
	}


	public function isImage (&$model, $data)
	{
		$data = array_shift($data);

        switch ($data['type'])
        {
            case 'image/jpeg':
            case 'image/png':
                return true;
            default:
                return false;
        }
	}
}