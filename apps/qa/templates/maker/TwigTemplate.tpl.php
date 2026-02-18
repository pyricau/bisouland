{% extends 'base.html.twig' %}

{% block title %}<?php echo $action_title; ?> - Qalin{% endblock %}

{% block body %}
    <h2>Action: <?php echo $action_title; ?></h2>
    <form data-api="/api/v1/actions/<?php echo $action_kebab; ?>" data-expect="201">
<?php foreach ($action_parameters as $param) { ?>
        <label for="<?php echo $param['name']; ?>"><?php echo ucfirst($param['name']); ?></label>
        <input class="u-full-width" type="<?php echo 'int' === $param['type'] ? 'number' : 'text'; ?>" id="<?php echo $param['name']; ?>" name="<?php echo $param['name']; ?>"<?php if (null !== $param['default']) { ?> value="<?php echo $param['default']; ?>"<?php } else { ?> required<?php } ?>>
<?php } ?>
        <button class="button-primary" type="submit"><?php echo $action_title; ?></button>
    </form>
    <div class="result"></div>
{% endblock %}
