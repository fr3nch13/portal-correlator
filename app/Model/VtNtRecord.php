<?php
App::uses('AppModel', 'Model');
/**
 * VtNtRecord Model
 *
 * @property VectorLookup $VectorLookup
 * @property VectorSrc $VectorSrc
 * @property VectorDst $VectorDst
 */
class VtNtRecord extends AppModel 
{
	public $displayField = 'vector_lookup_id';
	
	public $validate = array(
		'vector_lookup_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'vector_src_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'vector_dst_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'src_port' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'dst_port' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'VectorLookup' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_lookup_id',
		),
		'VectorSrc' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_src_id',
		),
		'VectorDst' => array(
			'className' => 'Vector',
			'foreignKey' => 'vector_dst_id',
		)
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'VectorLookup.vector',
		'VectorSrc.vector',
		'VectorDst.vector',
		'VtNtRecord.protocol',
		'VtNtRecord.src_port',
		'VtNtRecord.dst_port',
	);
	
	public function checkAdd(
		$vector_lookup_id = false, 
		$vector_src_id = false, 
		$src_port = false, 
		$vector_dst_id = false, 
		$dst_port = false, 
		$data = array())
	{
		if(!$vector_lookup_id) return false;
		if(!$vector_src_id) return false;
		if(!$vector_dst_id) return false;
		
		$id = false;
		
		if(!$id = $this->field('id', array(
			'vector_lookup_id' => $vector_lookup_id,
			'vector_src_id' => $vector_src_id,
			'vector_dst_id' => $vector_dst_id,
			'src_port' => $src_port,
			'dst_port' => $dst_port,
		)))
		{
			$this->create();
			$data['vector_lookup_id'] = $vector_lookup_id;
			$data['vector_src_id'] = $vector_src_id;
			$data['vector_dst_id'] = $vector_dst_id;
			$data['src_port'] = $src_port;
			$data['dst_port'] = $dst_port;
			$data['first_seen'] = date('Y-m-d H:i:s');
		}
		else
		{
			$this->id = $id;
		}
			
		$data['last_seen'] = date('Y-m-d H:i:s');
		$this->data = $data;
		
		if($this->save($this->data))
		{
			$id = $this->id;
		}
		return $id;
	}
}
