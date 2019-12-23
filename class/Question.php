<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Response.php';

class Question extends Model{
	public $title;
	public $description;
	public $type;
	public $pos;
	public $responses;

	protected $choices = array();

	const tableName = 'questions';

	const TYPE_YESORNO = 'yn';
	const TYPE_SLIDER = 'slider';
	const TYPE_OPTION = 'option';
	const TYPE_EXPANDED_OPTION = 'expanded_option';
	const TYPE_PARAGRAPH = 'paragraph'; 
	const TYPE_TEXT = 'text';
	const TYPE_CHECKBOX = 'checkbox';

	public function __construct($id, $title, $description, $type, $pos, $choices){
		$this->title = $title;
		$this->description = $description;
		$this->type = $type;
		$this->pos = $pos;

		$this->choices = json_decode($choices);
		$this->responses = Response::all()->equals('question_id', $id)->get();
	}

	public function getResponses(){
		return $this->responses;
	}

	public function getHTML(){
		switch($this->type){
			case self::TYPE_TEXT:
				return "<div class='form-group col-sm-12'>

			              <label for='{$this->pk}'><h4>{$this->title}<small> {$this->description}</small></h4></label>

			              <input type='text' class='form-control' name='{$this->pk}' required placeholder='Enter response' />

			          </div>";
			case self::TYPE_PARAGRAPH:
			    return "<div class='form-group col-sm-12'>

			              <label for='{$this->pk}'><h4>{$this->title}<small> {$this->description}</small></h4></label>

			              <textarea class='form-control' rows='5' maxlength='5000' name='{$this->pk}' required></textarea>

			          </div>";
			case self::TYPE_YESORNO:
			    return "<div class='col-sm-12'>

			        <h4>{$this->title}<small> {$this->description}</small></h4><br>

			        <div class='btn-group' data-toggle='buttons'>

			          <label class='btn btn-primary btn-lg'>

			            <input type='radio' name='{$this->pk}' id='option2' autocomplete='off' value='Yes'> Yes

			          </label>

			          <label class='btn btn-primary btn-lg'>

			            <input type='radio' name='{$this->pk}' id='option3' autocomplete='off' value='No'> No

			          </label>

			        </div>

			     </div>";
			case self::TYPE_OPTION:
			case self::TYPE_EXPANDED_OPTION:
			    $return = "<div class='form-group col-sm-12' style='margin-top:13px'>

			         <label for='{$this->pk}'><h4>{$this->title}<small> {$this->description}</small></h4></label>

			        <select name='{$this->pk}' " . ($this->type == self::TYPE_EXPANDED_OPTION ? 'multiple' : '') . " class='form-control'>";

			          foreach($this->choices as $choice){
			            $return .= "<option>{$choice}</option>";
			          }

			        $return .= "</select>

			    </div>";
			    return $return;
			case self::TYPE_CHECKBOX:
				$return = "<div class='form-group col-sm-12' style='margin-top:13px'>

			        <label for='{$this->pk}'><h4>{$this->title}<small> {$this->description}</small></h4></label>";
		        	foreach($this->choices as $choice){
		        		$return .= "<input type='checkbox' name='{$this->pk}[]'>{$choice}</option>";
		        	}

			        $return .= "</div>";
			    return $return;
			case self::TYPE_SLIDER:
			    $return = "<script>
			      $(document).ready(function(){
			      $('#{$this->pk}slider')
			          .slider({
			              min: {$this->choices[0]},
			              max: {$this->choices[1]},
			              change: function(event, ui) {
			                $('#{$this->pk}').attr('value', ui.value);
			              }
			          })
			          .slider('pips', {
			              rest: 'label'
			          })
			      });
			      </script>";
			    $return .= "<div class='form-group col-sm-12'><label><h4>{$this->title} <small>{$this->description}</small></h4></label><div id='{$this->pk}slider'></div></div>";
			    $return .= "<input type='hidden' name='{$this->pk}' id='{$this->pk}'/>";
			    return $return;
			default:
			    return "<h5>unknown question type</h5>";
		}
	}
}

