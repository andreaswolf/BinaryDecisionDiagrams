<?php
namespace AndreasWolf\BinaryDecisionDiagrams;

/**
 * Implementation of a BDD.
 *
 * See Andersen, Henrik: An Introduction to Binary Decision Diagrams
 * <http://configit.com/fileadmin/Configit/Documents/bdd-eap.pdf> for details on the implementation.
 *
 * @author Andreas Wolf <aw@foundata.net>
 */
class BinaryDecisionDiagram {

	/**
	 * The variable names covered by this diagram, in the order they are covered
	 * @var string[]
	 */
	protected $variableNames;

	/**
	 * @var array
	 */
	protected $nodes;

	/**
	 * @param string[] $variables The variable names to be used in the BDD.
	 * @param array $nodes The nodes of the decision diagram. An array of arrays with the values [variable name, next node for F, next node for T]. The key in this array is the actual number of the node. The first two keys should be empty, as they represent FALSE and TRUE decision output
	 */
	public function __construct(array $variables, array $nodes) {
		$this->variableNames = $variables;
		$this->nodes = $nodes;
		// ensure the two first nodes are representations of FALSE/TRUE decision outcome
		$this->nodes[0] = array(FALSE);
		$this->nodes[1] = array(TRUE);
	}

	/**
	 * Evaluates the decision diagram to find the correct output for the given input.
	 *
	 * @param array $input
	 * @return boolean
	 */
	public function getResultForInput(array $input) {
		$currentNode = $this->nodes[count($this->nodes) - 1];
		foreach ($this->variableNames as $variable) {
			if ($input[$variable] === TRUE) {
				$nextNodeId = $currentNode[2];
			} elseif ($input[$variable] === FALSE) {
				$nextNodeId = $currentNode[1];
			}

			$currentNode = $this->nodes[$nextNodeId];
			if ($nextNodeId <= 1) {
				break;
			}
		}
		return $currentNode[0];
	}

}