<?php
require 'Model.php';

class Question extends Model{
	public $title;
	public $description;
	public $type;
	public $pos;

	private $choices = array();

	const tableName = 'questions';

	const YESORNO = 'yn';
	const SLIDER = 'slider';
	const OPTION = 'option';
	const EXPANDED_OPTION = 'expanded_option';
	const PARAGRAPH = 'paragraph'; 
	const TEXT = 'text';
	const CHECKBOX = 'checkbox';

	public function __construct($title, $description, $type, $pos, $choices){
		$this->title = $title;
		$this->description = $description;
		$this->type = $type;
		$this->pos = $pos;

		$this->choices = json_decode($choices);
	}

	public function getHTML(){
		switch($this->type){
			case self::TEXT:
				return "<div class='form-group col-sm-12'>

			              <label for='{$this->id}'><h4>{$this->title}<small> {$this->description}</small></h4></label>

			              <input type='text' class='form-control' name='{$this->id}' required placeholder='Enter response' />

			          </div>";
			case self::PARAGRAPH:
			    return "<div class='form-group col-sm-12'>

			              <label for='{$this->id}'><h4>{$this->title}<small> {$this->description}</small></h4></label>

			              <textarea class='form-control' rows='5' maxlength='5000' name='{$this->id}' required></textarea>

			          </div>";
			case self::YESORNO:
			    return "<div class='col-sm-12'>

			        <h4>{$this->title}<small> {$this->description}</small></h4><br>

			        <div class='btn-group' data-toggle='buttons'>

			          <label class='btn btn-primary btn-lg'>

			            <input type='radio' name='{$this->id}' id='option2' autocomplete='off' value='Yes'> Yes

			          </label>

			          <label class='btn btn-primary btn-lg'>

			            <input type='radio' name='{$this->id}' id='option3' autocomplete='off' value='No'> No

			          </label>

			        </div>

			     </div>";
			case self::OPTION:
			case self::EXPANDED_OPTION:
			    $return = "<div class='form-group col-sm-12' style='margin-top:13px'>

			         <label for='{$this->id}'><h4>{$this->title}<small> {$this->description}</small></h4></label>

			        <select name='{$this->id}' " . ($this->type == self::EXPANDED_OPTION ? 'multiple' : '') . " class='form-control'>";

			          foreach($this->choices as $choice){
			            $return .= "<option>{$choice}</option>";
			          }

			        $return .= "</select>

			    </div>";
			    return $return;
			case self::SLIDER:
			    $return = "<script>
			      $(document).ready(function(){
			      $('#{$this->id}slider')
			          .slider({
			              min: {$this->choices[0]},
			              max: {$this->choices[1]},
			              change: function(event, ui) {
			                $('#{$this->id}').attr('value', ui.value);
			              }
			          })
			          .slider('pips', {
			              rest: 'label'
			          })
			      });
			      </script>";
			    $return .= "<div class='form-group col-sm-12'><label><h4>{$this->title} <small>{$this->description}</small></h4></label><div id='{$this->id}slider'></div></div>";
			    $return .= "<input type='hidden' name='{$this->id}' id='{$this->id}'/>";
			    return $return;
			default:
			    return "<h5>unknown question type</h5>";
		}
	}
}

