@extends('layouts.landing-page-layout')

@section('tab-title') - Examples and good practices @endsection @section('content')
    <style>
        .highlight {
            color: #26a69a;
        }

        .title-text {
            display: inline-block;
            font-size: 4vh;
            width: 70%;
            vertical-align: middle;
        }
.collapsible-header {
    color: #26a69a;
}
        .collapsible-body > ul li {
            list-style-type: disc !important;
        }
        .collapsible-body ul {
            padding-left: 40px;

        }
        .collapsible-body > ul ul li {
            list-style-type: circle !important;
        }
        .collapsible-body ol {
            padding-left: 40px;
        }
    </style>
    {{--    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">--}}
    <div class="container create-class-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <div class="header">Examples and good practices</div>
                        Here, you can find some more information on how to use audience response systems (ARSs) in your
                        courses.
                        <ul class="collapsible">
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Multiple-choice or
                                    multiple- selection questions?
                                </div>
                                <div class="collapsible-body"><span>In multiple-choice questions, only one of the choices is correct, while in multiple-selection questions the participant can select several of the available choices. The selection, then, can be graded as one complex answer or as individual answers to the same question. SAGA supports only multiple-choice questions.</span>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Closed-type or
                                    open-type?
                                </div>
                                <div class="collapsible-body"><span>In a closed-type multiple-choice question, all available choices are predefined by the quiz-creator. On the contrary, in an open-ended question, the participant is free to add his/her own text as an answer. SAGA supports only closed-type multiple-choice questions. However, if you decide to use justifications in your quizzes, then the students will be able to justify their answers by writing text. In other words, the quiz questions will be closed-type (e.g., “What year was the University of Twente established? A: 1960; B: 1961; C: 1963; D: 1964”), but the accompanying justification question will be an open-type one.</span>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>What can you expect
                                    from using ARSs in your courses? (Benefits)
                                </div>
                                <div class="collapsible-body">
                                    <ul>
                                        <li><span
                                                class="highlight">The same question is posed to all students.</span>
                                            This means that all students will have to reflect on the question and
                                            provide an answer. The alternative would be to ask a question orally to the
                                            audience (or to a specific participant), in which case not all students will
                                            think of an answer.
                                        </li>
                                        <li><span class="highlight">Immediate feedback.</span> With an ARS, it is
                                            possible to provide immediate and personalized feedback to all students.
                                            Personalized feedback means feedback based on the answer or the progress of
                                            the student in the quiz.
                                        </li>
                                        <li><span class="highlight">Anonymity provides psychological safety and acceptance.</span>
                                            It has been noted in several studies that students are more comfortable
                                            answering questions privately in a quiz than answering them orally in front
                                            of an audience.
                                        </li>
                                        <li><span class="highlight">Increased engagement and motivation.</span> Research
                                            findings have shown that in courses where ARSs are used, students pay more
                                            attention in the class and tend to have higher attendance rates. Having been
                                            engaged with the questions of the quiz, the students are also more likely to
                                            then ask or answer questions during the lecture. Finally, students usually
                                            find classes more enjoyable and satisfying.
                                        </li>
                                        <li><span class="highlight">Students are invested in their answers.</span>
                                            Another reported finding of the use of ARSs is that students are more prone
                                            to defend their answers in group/classroom discussions.
                                        </li>
                                        <li><span class="highlight">ARS are routinely credited for developing critical thinking.</span>
                                            A multiple-choice question does not have to be about memory recall. It may
                                            very well require the application of critical thinking, knowledge transfer,
                                            and creativity. The successful use of an ARS is based heavily on the quality
                                            of the questions. And, writing challenging and effective questions is not a
                                            trivial task!
                                        </li>
                                        <li><span
                                                class="highlight">Uncovering preconceptions and assumptions.</span> By
                                            getting the answers of the classroom in a quiz, the teacher may be able to
                                            evaluate the classroom’s level of understanding and modify instruction
                                            accordingly.
                                        </li>
                                        <li><span class="highlight">Monitoring students’ progress.</span> With ARSs, it
                                            is possible to monitor the progress of a student within a quiz or a series
                                            of quizzes. Progress, of course, does not necessarily mean form assessment.
                                        </li>
                                        <li><span
                                                class="highlight">Low technological threshold for the teacher.</span> An
                                            ARS is a rather simple technological tool. Most time will be spent on
                                            writing questions of high quality.
                                        </li>
                                        <li><span
                                                class="highlight">Increased retention and knowledge acquisition.</span>
                                            Studies have repeatedly reported that by using ARSs, students tend to
                                            remember more about the lectures and the course, in general, and they have
                                            better overall performance in the formal assessment of the course.
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>What you should not
                                    expect from using ARSs in your courses? (Shortcomings)
                                </div>
                                <div class="collapsible-body">
                            <ul>

                            <li><span class="highlight">Some studies in the literature reported low or non-significant impact on learning.</span> On one hand, the good thing is that there are no studies that show the ARSs impeded students’ knowledge acquisition. On the other hand, there is an ongoing discussion about the potential of ARS and whether the educational gains can be attributed to the tool or the pedagogy. In any case, any technology must be meaningfully integrated into a sound pedagogical approach. Using a tool without some thought of the instructional model and the learning goals the tools should serve would probably lead to shallow learning experiences for the students.
                            </li>
                                <li><span class="highlight">ARSs are not appropriate for elaboration.</span> There are several higher-level learning goals that are strongly linked with the ability of students to demonstrate or elaborate their answers. Inherently, ARSs are based on closed-type questions and offer little room for elaboration. SAGA’s justification question is an attempt to allow student voice within the confined space of multiple-choice questions.

                                </li>
                                <li><span class="highlight">Writing challenging and effective questions is not a trivial task! </span> </li>
                            </ul></div>

                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>How to write good
                                    questions?
                                </div>
                                <div class="collapsible-body">This is a hard question to fully answer, but there are
                                    several guidelines to help you write compelling questions.
                                    <ul>
                                        <li>
                                            <span class="highlight"> Keep it simple!</span> Both the question and the
                                            answers must be clear and easily understood. The challenge should NOT be if
                                            the students understand the question.
                                        </li>
                                        <li>
                                            <span class="highlight">Avoid negatives.</span> Questions such as “Which of
                                            the following is not correct?” can be tricky for the students as they are
                                            trained to focus on the correct answer. If you do need to use negatives,
                                            then emphasize negation in the question phrasing (e.g., “…is NOT correct?”).
                                        </li>
                                        <li>
                                            <span class="highlight">Have the question choices with similar length and complexity.</span>
                                            Longer and more detailed choices are usually a “tell” of a correct answer.
                                            You should not provide hints as this counteracts the purpose of the quiz. In
                                            addition, a purposefully detailed wrong choice that tries to trick students
                                            is also counterproductive. The goal of the quiz is to objectively identify
                                            the level of knowledge and understanding of students. Therefore, it is
                                            necessary for all students to have a good understanding of the questions and
                                            their choices.
                                        </li>
                                        <li>
                                            <span
                                                class="highlight">Spread out the position of the correct answer.</span>
                                            It has been a running joke that if you do not know the answer, then you
                                            should choose “B” as this is where the correct answer is mostly places.
                                            Actually, quiz-creators tend to have the correct answer in the last places
                                            (e.g., “C” or “D”) – it is easier to think of a few wrong answers before you
                                            write the correct one. Review the quiz and check where the correct answer is
                                            placed, but make sure that the choices of a question appear in a meaningful
                                            order (e.g., descending or ascending, in case of numbers or dates).
                                        </li>
                                        <li>
                                            <span class="highlight">The question should be meaningful even without the choices.</span>
                                            Avoid questions that will continue into the choices (also questions such as
                                            “Which of the following is correct?”). This type of phrasing is based on
                                            students’ inferential ability and may interfere with their ability to
                                            demonstrate their actual level of knowledge.
                                        </li>
                                        <li>
                                            <span class="highlight">The question should not contain irrelevant or tricky information.</span>
                                            This decreases the reliability and validity of the quiz. As mentioned
                                            earlier, the goal is to assess students’ knowledge and understanding and not
                                            their ability to understand the quiz.

                                        </li>
                                        <li>
                                            <span
                                                class="highlight">Fill-in-the-blank questions should be avoided.</span>
                                            They increase the cognitive load of the students. So, they are more
                                            challenging, but for the wrong reasons.

                                        </li>
                                        <li>
                                            <span class="highlight">All answers should appear plausible.</span>
                                            The alternative answers should be close to the correct one. A knowledgeable
                                            student should be able to differentiate between subtle differences, but a
                                            less knowledgeable student should not.
                                        </li>
                                        <li>
                                            <span class="highlight">Less knowledgeable students will go for the most informative answer.</span>
                                            This is a very common gaming strategy for students that do not know the
                                            right answer. Their assumption is that it would be easier for the teacher to
                                            be more precise in the correct answer than in the made-up alternatives. So,
                                            make sure that you do not have this “tell” in your questions.

                                        </li>
                                        <li>
                                            <span class="highlight">The question choice “All of the above” should be avoided.</span>
                                            The students will need to know that at least two answers are correct to
                                            answer the question.

                                        </li>
                                        <li>
                                            <span class="highlight">The question choice “None of the above” should be avoided.</span>
                                            If “None of the above” is the correct answer it does not provide information
                                            that the students actually know the correct answer to the question. In
                                            addition, if “None of the above” is selected, but is a wrong answer, it does
                                            not provide accurate measurement of students’ knowledge. The students may
                                            actually know that some of the choices are wrong or they have no knowledge
                                            at all.

                                        </li>
                                        <li>
                                            <span class="highlight">The number of answers may vary across questions – but it is best to keep it the same.</span>
                                            Plausible alternatives act as distractors for less knowledgeable students,
                                            so researchers on assessment reported that there is no significant
                                            difference in quiz reliability in relation to the number of alternatives.
                                            But, it is better to keep the number of choices the same across your quiz
                                            because it will be easier for you to provide a homogenous experience to the
                                            students.

                                        </li>
                                        <li>
                                            <span class="highlight">Do not provide information in one question that can be used to answer another question.</span>
                                            This includes the information given in the choices of a question.

                                        </li>
                                        <li>
                                            <span class="highlight">Use mutlilogical thinking questions to assess higher-order thinking.</span>
                                            Multilogical thinking is thinking that requires knowledge of more than one
                                            fact to logically and systematically apply concepts to a problem.

                                        </li>
                                        <li>
                                            <span class="highlight">Require knowledge transfer and application to assess critical thinking.</span>
                                            Students may need to apply several principles and theories to reach the
                                            correct answer. Having a short question does not mean that the students will
                                            find the answer easily or quickly. Critical thinking should still lead to
                                            only one correct answer.

                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>Where in the course
                                    timeline to use an ARS tool?
                                </div>
                                <div class="collapsible-body">
                                    Depending on the type and purpose of the quiz, you can use it at different times in
                                    your course’s timeline to address different learning goals.
                                    <br><span class="highlight">In the beginning of a lecture.</span>
                                    <ul>
                                        <li>Identify misconceptions.</li>
                                        <li>Provide feedback and time to the teacher to modify instruction
                                            accordingly.
                                        </li>
                                        <li>If combined with a flipped-classroom approach, then this can:
                                            <ul>
                                                <li>Provide an extra motive to the student to engage with the
                                                    material.
                                                </li>
                                                <li>Provide the opportunity for students to review material before the
                                                    lecture.
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                    <span class="highlight">During a lecture.</span>
                                    <ul>
                                        <li>Identify misconceptions.</li>
                                        <li>Provide feedback to the teacher to modify instruction accordingly.</li>
                                        <li>Provide the opportunity to students for reflection on what has been presented and discussed so far.</li>
                                    </ul>
                                    <span class="highlight">At the end of a lecture.</span>
                                    <ul>
                                        <li>Provide feedback to the teacher on students’ understanding of the lecture.</li>
                                        <li>Provide the opportunity to students for reflection on the whole course.
                                            <ul><li>This further enhances retention.</li></ul>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>The underlying pedagogy</div>
                                <div class="collapsible-body">
                                    The use of audience response systems in the classroom became a popular technique of instruction because, in large part, of the work of Eric Mazur, a Professor of Physics and Applied Physics at Harvard University. His underlying pedagogy in using ARSs was called Peer Instruction and provided the basis for using ARS in the classroom. While variations of the approach exist now, many tools (including SAGA) are still based on the basic principles of Mazur’s approach.<br>
                                    The Peer Instruction approach of using ARSs in the classroom includes the following steps:
                                    <ol>
                                        <li>The teacher poses a question.</li>
                                            <li>The students respond individually.</li>
                                            <li>Aggregated feedback on the responses is provided to the students.</li>
                                            <li>The students discuss their answers in small groups.</li>
                                            <li>The students respond again either individually or in groups.</li>
                                            <li>The correct answer is presented by the teacher.</li>
                                            <li>The teacher may organize class-wide discussions or provide further explanations.</li>
                                    </ol>
                                    <br>There are three variations of the above model in SAGA:

                                    <ol>
                                    <li>The teacher poses a question.</li>
                                    <li>The students respond individually.</li>
                                    <li>Rich aggregated feedback that includes several objective and subjective metrics based on the responses is provided to the students.</li>
                                    <li>Group discussion is not happening to adhere to lecture time constraints.</li>
                                    <li>The students see their previous answers and decide whether to revise or not individually.</li>
                                    <li>The correct answer is presented by the teacher.</li>
                                    <li>The teacher may organize class-wide discussions or provide further explanations.</li>
                            </ol>
                                    <br>You can develop your own approach, but the basic principle is to let the student know at some point what the classroom thinks on the same question and if you cannot allocate the time for classroom discussion, then you will need to provide additional information on the class to the students – this is what the additional feedback metrics in SAGA are trying to do.
                                </div></li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">add</i>What feedback metrics are available in SAGA?</div>
                                <div class="collapsible-body">
                                    Several feedback metrics have been used in past studies with different versions of SAGA. These metrics aimed at providing a better picture of the audience to the student:
                                    <br><span class="highlight">Objective metrics</span>
                                    <ul>
                                        <li>Percentage: How many students selected each choice?</li>
                                        <li>Past performance: How did they perform in previous quizzes?</li>
                                    </ul>
                                    <br><span class="highlight">Subjective (self-reported)</span>
                                    <ul>
                                        <li>Preparation: How prepared do they feel to take the quiz?</li>
                                        <li>Confidence: How confident do they feel that they got the right answer?</li>
                                        <li>Self-assessment: How well do they think they did in the whole quiz?</li>
                                        <li>Justification: Why did they select that particular choice?</li>
                                    </ul>
                                    <br>Past performance and self-assessment are not available in the current version of SAGA. However, you are also able to create your own accompanying questions on the feedback metrics of your choice.
                                </div></li>
                        </ul>
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

